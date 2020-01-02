<?php
 
/**
 * Following code will one personnel tasks info from boss tasks table.
 * The returned columns are id, titles, descriptions, startings, endings, repetitions, locations, positions, geofence dateadded, datechanged.
 * Arguments are:
 * id==boss id.
 * Returns are:
 * tasks rows
 * success==1 successful get
 * success==0 for missing certificates info
 * success==0 for id argument missing
 **/
// array for JSON response
$response = array();
 
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// connecting to db
$db = new DB_CONNECT();
 
// check for post data
if (isset($_POST['bossid']) && isset($_POST['userid']) && isset($_POST['position']))
    {
    $bossid = $_POST['bossid'];
	$userid=$_POST['userid'];
	$position=$_POST['position'];
	//get the table name
	$head=makeTableName((string)$bossid);
	$tablename=$head.'_tasks';
 
    // get an account from staff accounts table
    $sqlcommand="SELECT *FROM $tablename ";
     $result = mysqli_query($db::$connection,$sqlcommand) ;
 
    if (!empty($result))
        {
        // check for empty result
        if (mysqli_num_rows($result) > 0) 
            {
            //looping through all tasks rows
            $response["tasks"]=array();
            while ($row= mysqli_fetch_array($result))
            {
                //temp certificates row
                  $tasks=array();
                  $tasks['id']=$row['id'];
                  $tasks['title']=$row['titles'];
                  $tasks['description']=$row['descriptions'];
                  $tasks['starting']=$row['startings'];
                  $tasks['ending']=$row['endings'];
                  $tasks['repetition']=$row['repetitions'];
				  $tasks['location']=$row['locations'];
				  $tasks['position']=$row['positions'];
				  $tasks['geofence']=$row['geofence'];
                  $tasks['dateadded']=$row['dateadded'];
				  $tasks['datechanged']=$row['datechanged'];
				  //find out if the task's position is users
				  //the position is formed by combining the position name and id number separated by ':'
				  //if the id number is 0 it means the tasks belongs to a position group otherwise the number represents the user id
				  $position_array=explode(":",$tasks['position']);
				  if($position_array[1]=='0')
				  {
					  if($position_array[0]!=$position)
						  continue;
				  }
				  else if($position_array[1]!=$userid)
				  {
					  continue;
				  }
				  //find out if the taskis one of the following
				  //pending---the boss has added a task to be done in the future
				  //in progress---the task is been carried out now
				  //completed---the task has already been completed
				  //overdue---the task was never done
				  //late---the task was done past deadline
                  //push a single schema into array
				  //---the tasks are in the form "20/12/2018s3:00 PM,5id20/12/2018s3:00 PM,6id20/12/2018s3:00 PM"
				  //---for starting and ending time the first potion of the array is the deadline time and the rest are individual times for the users
				  //we will compare the users times with the deadline 
				  $pending=0; $in_progress=0; $completed=0; $overdue=0; $late=0;
				  $pending_ids=''; $in_progress_ids=''; $completed_ids=''; $overdue_ids=''; $late_ids='';
				  $date_now=new DateTime("now");
				  $task_array_start=explode(",",$tasks['starting']);
				  $task_array_end=explode(",",$tasks['ending']);
				  $date_start=DateTime::createFromFormat('d/m/Y H:i A',str_replace("s"," ",$task_array_start[0]));
				  $date_end=DateTime::createFromFormat('d/m/Y H:i A',str_replace("s"," ",$task_array_end[0]));
				  //check if the task will happen in the future
				  if($date_start>$date_now && count($task_array_start)==1)
					  $pending=1;
				  else if($date_end<$date_now && count($task_array_start)==1)
					  $overdue=1;
				  else
				  {
					  for($c=1; $c<count($task_array_start); $c+=1)
					  {
						  $user_data=explode("id",$task_array_start[$c]);
						  $user_id=$user_data[0];
						  $user_date_start=str_replace("s"," ",$user_data[1]);
						  $user_date_start= DateTime::createFromFormat('d/m/Y H:i A',$user_date_start);
						  $user_date_end="";
						  //find the user's end time
						  for($d=0; $d<count($task_array_end); $d+=1)
						  {
							  //note when using equality sign '!=', the equation will yield false since the string to be found is the first word therefore we use '!==' 
							  if(strpos($task_array_end[$d],$user_id."id") !==false)
								  $user_date_end=str_replace("s"," ",$task_array_end[$d]);
						  }
						  // if the user's end time is missing it means the task is in progress
						  if($user_date_end=="")
						  {
							  $in_progress+=1;
							  $in_progress_ids.=$user_id.':';
						  }
						  else 
						  {
							  $user_date_end=DateTime::createFromFormat('d/m/Y H:i A',$user_date_end);
							  //check for completed	
							  //the user did the task after or at start time and finished before or at end time
							  //--- or the user did the task and finished before or at start and end time
                              if(($user_date_start>=$date_start && $user_date_end<=$date_end) || ($user_date_start<=$date_start && $user_date_end<=$date_end))	
							  {
									$completed+=1;
									$completed_ids.=$user_id.':';
							  }
								//the user finished the task late
								else if($user_date_end>$date_end)
								{
									$late+=1;
									$late_ids.=$user_id.':';
								}
								//the user never did the task
								else 
								{
									$overdue+=1;
									$overdue_ids.=$user_id.':';
								}
						  }
						  
					  }
				  }
				  $tasks['p']=$pending;
				  $tasks['i']=$in_progress;
				  $tasks['c']=$completed;
				  $tasks['o']=$overdue;
				  $tasks['l']=$late;
				  $tasks['pids']=$pending_ids;
				  $tasks['inids']=$in_progress_ids;
				  $tasks['cids']=$completed_ids;
				  $tasks['oids']=$overdue_ids;
				  $tasks['lids']=$late_ids;				  
                  array_push($response["tasks"], $tasks); 
                
            }
            $response["success"] = 1; 
            $response["message"] = "found them";
            echo json_encode($response);

        }
        else
            {
            // no product found
            $response["success"] = 0;
            $response["message"] = "no tasks". mysqli_error($db::$connection);
            echo json_encode($response);
        }
    }
    else 
        {
        // no product found
        $response["success"] = 0;
        $response["message"] = "no tasks found". mysqli_error($db::$connection);
       // echo no users JSON
        echo json_encode($response);
    }
}
else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
    echo json_encode($response);
}
//get the new table name
function makeTableName($id)
{
    $array= str_split($id);
    $name='';
    for($count=0; $count<sizeof($array); $count++)
    {
        switch ($array[$count])
        {
            case 0:
                $name=$name.'zero';
                break;
            case 1:
                $name=$name.'one';
                break;
            case 2:
                $name=$name.'two';
                break;
            case 3:
                $name=$name.'three';
                break;
            case 4:
                $name=$name.'four';
                break;
            case 5:
                $name=$name.'five';
                break;
            case 6:
                $name=$name.'six';
                break;
            case 7:
                $name=$name.'seven';
                break;
            case 8:
                $name=$name.'eight';
                break;
            case 9:
                $name=$name.'nine';
                break;
            default :
                $name=$name.'NON';
        }
    }
    return $name;    
}
 
?>