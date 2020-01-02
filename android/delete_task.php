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

// check for required fields
if (isset($_POST['bossid']) && isset($_POST['taskid']))
    {
		//store the posted values in variables
		$bossid = $_POST['bossid'];
		$taskid = $_POST['taskid'];
		
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$bossid);
		$tablename=$head.'_tasks';
		//delete
		$sql_command_delete_row ="DELETE FROM $tablename WHERE id = $taskid";
		$result_delete_row=mysqli_query($db::$connection,$sql_command_delete_row) ;
		if($result_delete_row)
		{
			$response["success"] = 1;
			$response["message"] = "removed";
			echo json_encode($response);
		}
		else 
		{
			$response["success"] = -2;
			$response["message"] = "error deleting.". mysqli_error($db::$connection);
			echo json_encode($response);
		}
			
	} 
		

 else
	 {
    // required field is missing
    $response["success"] = -1;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
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