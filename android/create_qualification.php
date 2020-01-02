<?php
 
/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */
 // array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();

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
 
// check for required fields
if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['which']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$name = $_POST['name'];
		$which=$_POST['which'];
		
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_personnel_matrix';
		 		
		//check if the email is already registered
		//NOTE the tablename is not in quotes and the column is in quotes coz...
        $sql_command_check="SHOW COLUMNS FROM $tablename LIKE '$name'";
		$result_check = mysqli_query($db::$connection,$sql_command_check) ;
		if(!$result_check || mysqli_num_rows($result_check)>0)
			{
				// failed to insert column
				$response["success"] = -2;
				$response["message"] = "column already there.". mysqli_error($db::$connection);
				echo json_encode($response); 
			}
			else
			{
				//get the lengths
				$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
				$result = mysqli_query($db::$connection,$sql_command) ;
				$result = mysqli_fetch_array($result);
				$lengths=$result['lengths'];
				if($lengths=="NULL" || $lengths=="null" || $lengths=="")
				{
					$new_lengths="";
					if($which=='m')
						$new_lengths="m,1:j,0:t,0";
					else if($which=='j')
						$new_lengths="m,0:j,1:t,0";
					$sql_command_add="ALTER TABLE $tablename ADD $name TEXT NULL AFTER userid";
					$result = mysqli_query($db::$connection,$sql_command_add) ;
					if($result)
					{
						// mysql updating lengths
						$sql_command_update="UPDATE contractors_accounts SET lengths='$new_lengths', datechanged='$today' WHERE id=$id";
						$result_update = mysqli_query($db::$connection,$sql_command_update) ;
						if($result_update)
						{
							$response["success"] = 1;
							$response["message"] = "added";
							echo json_encode($response);
						}
						else
						{
							$response["success"] = -2;
							$response["message"] = "lengths not updated.". mysqli_error($db::$connection);
							echo json_encode($response);
						}
					}
					else
					{
						$response["success"] = -1;
						$response["message"] = "column not added.". mysqli_error($db::$connection);
						echo json_encode($response);
					}
					
				}
				else 
				{
					//get individual column groups
					$lengths_pieces=explode(":",$lengths);
					$mand=explode(",",$lengths_pieces[0]);
					$jobs=explode(",",$lengths_pieces[1]);
					$trade=explode(",",$lengths_pieces[2]);
					 //getting the table trade columns
					$columnnames=array();
					$sqlcolumns="SHOW COLUMNS FROM $tablename";    
					$resultcolumns = mysqli_query($db::$connection,$sqlcolumns) ;
					$numColumns= mysqli_num_rows($resultcolumns);
					while ($row= mysqli_fetch_array($resultcolumns))
						{
							$columnnames[]=$row['Field'];
						}
					$column_after="";
					if($which=='m')
						$column_after="userid";
					else if($which=='j')
						$column_after=$columnnames[1+$mand[1]];
					$sql_command_add="ALTER TABLE $tablename ADD $name TEXT NULL AFTER $column_after";
					$result = mysqli_query($db::$connection,$sql_command_add) ;
					if($result)
					{
						$new_lengths="";
						if($which=='m')
							$new_lengths='m,'.($mand[1]+1).':j,'.$jobs[1].':t,'.$trade[1];
						else if($which=='j')
							$new_lengths='m,'.$mand[1].':j,'.($jobs[1]+1).':t,'.$trade[1];
						//update lengths
						$sql_command_update="UPDATE contractors_accounts SET lengths='$new_lengths', datechanged='$today' WHERE id=$id";
						$result_update = mysqli_query($db::$connection,$sql_command_update) ;
						if($result_update)
						{
							$response["success"] = 1;
							$response["message"] = "added";
							echo json_encode($response);
						}
						else
						{
							$response["success"] = -4;
							$response["message"] = "row not inserted.". mysqli_error($db::$connection);
							echo json_encode($response);
						}
												
					}
					else
					{
						$response["success"] = -3;
						$response["message"] = "column not added.". mysqli_error($db::$connection);
						echo json_encode($response);
					}
					
					
				}
				
			}
			 
			
	} 
		

 else
	 {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
     echo json_encode($response);
	}
?>