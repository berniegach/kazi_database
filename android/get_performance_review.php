<?php
 
/**
 * Following code will all personnel tasks info from boss tasks table.
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
if (isset($_POST['id']))
    {
    $id = $_POST['id'];
	//get the table name
	$head=makeTableName((string)$id);
	$tablename=$head.'_performance_review';
	$tablename_matrix=$head.'_personnel_matrix';
	
	//first refresh the performance reviews
		//get the period
		//This can be 1 month, 3 months, 6 months and 1 year 
		$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
		$result = mysqli_query($db::$connection,$sql_command) ;
		$result = mysqli_fetch_array($result);
		$period=$result['reviewperiod'];
		 
		//get the employees list
		$sql_command_users="SELECT userid FROM $tablename_matrix WHERE userid > 0";
	    $result_users=mysqli_query($db::$connection,$sql_command_users);
		if (!empty($result_users) && mysqli_num_rows($result_users) > 0)
		{
			$count_users=0;
			while ($row= mysqli_fetch_array($result_users))
			{
				$users[$count_users]=$row['userid'];
				$count_users+=1;
			}
				
		}
		else
		{
			//there are no users so there is no reason to refresh the reviews
			$response["success"] = 0;
			$response["message"] = "no users". mysqli_error($db::$connection);
			echo json_encode($response);
		}
		
		$day= date("d");  
		$month= date("m");  
		$year= date("Y");  
		$checking_day=24;
		//the review notifications are generated from 25th of every month 
		//1 month
		if($period == '1')
		{
			if((int)$day>(int)$checking_day)
			{
				//add pending reviews for each user
				for($c=0; $c<count($users); $c++)
					createReview($db, $tablename, $users[$c], (int)$month, (int)$year );
			}
		}
		//3 months
		else if($period == '3')
		{
			if( ((int)$month==3 || (int)$month==6 || (int)$month==9 || (int)$month==12) && (int)$day>(int)$checking_day)
				for($c=0; $c<count($users); $c++)
					createReview($db, $tablename, $users[$c], (int)$month, (int)$year );
		}
		//6 months
		else if($period == '6')
		{
			if( ((int)$month==6 || (int)$month==12) && (int)$day>(int)$checking_day)
				for($c=0; $c<count($users); $c++)
					createReview($db, $tablename, $users[$c], (int)$month, (int)$year );
				
		}
		//1 year
		else if($period == '12')
		{
			if( (int)$month==12 && (int)$day>(int)$checking_day)
				for($c=0; $c<count($users); $c++)
					createReview($db, $tablename, $users[$c], (int)$month, (int)$year );
		}
		
		
    // get an account from staff accounts table
    $sqlcommand="SELECT *FROM $tablename ";
    $result = mysqli_query($db::$connection,$sqlcommand) ;
 
    if (!empty($result))
        {
        // check for empty result
        if (mysqli_num_rows($result) > 0) 
            {
            //looping through all tasks rows
            $response["reviews"]=array();
            while ($row= mysqli_fetch_array($result))
            {
                  $reviews=array();
                  $reviews['id']=$row['id'];
                  $reviews['userid']=$row['userid'];
                  $reviews['classes']=$row['classes'];
                  $reviews['reviewer']=$row['reviewer'];
                  $reviews['review']=$row['review'];
                  $reviews['toimprove']=$row['toimprove'];
				  $reviews['rating']=$row['rating'];
				  $reviews['themonth']=$row['themonth'];
				  $reviews['theyear']=$row['theyear'];
                  $reviews['dateadded']=$row['dateadded'];
				  $reviews['datechanged']=$row['datechanged'];
				   
                  array_push($response["reviews"], $reviews); 
                
            }
            $response["success"] = 1; 
            $response["message"] = "found them";
            echo json_encode($response);

        }
        else
            {
            // no product found
            $response["success"] = 0;
            $response["message"] = "no reviews". mysqli_error($db::$connection);
            echo json_encode($response);
        }
    }
    else 
        {
        // no product found
        $response["success"] = 0;
        $response["message"] = "no reviews found". mysqli_error($db::$connection);
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
function createReview($db, $table, $userid, $themonth, $theyear)
{
	//get current date
	$today= date("d-m-Y H:i");
	$sql_command_check="SELECT *FROM $table WHERE  userid = '$userid' AND classes = '0'AND themonth = '$themonth' AND theyear='$theyear'";
	$result_check = mysqli_query($db::$connection,$sql_command_check) ;
	if(!(!$result_check || mysqli_num_rows($result_check)>0))
	{
		//the notification is not there
        $sql_command_insert="INSERT INTO $table (userid,classes,themonth, theyear, dateadded)"
			. " VALUES('$userid', '0', '$themonth', '$theyear', '$today')";
		$result = mysqli_query($db::$connection,$sql_command_insert) ;
		if(!$result)
		{
			$response["success"] = 0;
			$response["message"] = "inserting review ". mysqli_error($db::$connection);
			echo json_encode($response);
		}
	}
}
 
?>