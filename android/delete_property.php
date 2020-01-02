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
if (isset($_POST['id']) && isset($_POST['propertyname']) && isset($_POST['which']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$propertynname = $_POST['propertyname'];
		$which=$_POST['which'];
		
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_equipments_matrix';
		//get the lengths
		$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
		$result = mysqli_query($db::$connection,$sql_command) ;
		$result = mysqli_fetch_array($result);
		$lengths=$result['lengthsequipments'];
		//get individual column groups
		$lengths_pieces=explode(":",$lengths);
		$mand=explode(",",$lengths_pieces[0]);
		$jobs=explode(",",$lengths_pieces[1]);
		$trade=explode(",",$lengths_pieces[2]);
		//delete row
		$sql_command_delete="ALTER TABLE $tablename DROP COLUMN $propertynname";
		$result_delete = mysqli_query($db::$connection,$sql_command_delete) ;
		if($result_delete)
		{
			//update lengths
			$new_lengths="";
			if($which=='m')
				$new_lengths='m,'.($mand[1]-1).':j,'.$jobs[1].':t,'.$trade[1];
			else if($which=='j')
				$new_lengths='m,'.$mand[1].':j,'.($jobs[1]-1).':t,'.$trade[1];
			$sql_command_update="UPDATE contractors_accounts SET lengthsequipments='$new_lengths', datechanged='$today' WHERE id=$id";
			$result_update = mysqli_query($db::$connection,$sql_command_update) ;
			if($result_update)
			{
				$response["success"] = 1;
				$response["message"] = "removed";
				echo json_encode($response);
			}
			else
			{
				$response["success"] = -3;
				$response["message"] = "lengths not updated.". mysqli_error($db::$connection);
				echo json_encode($response);
			}
			
		}
		else
		{
			$response["success"] = -1;
			$response["message"] = "trade not deleted.". mysqli_error($db::$connection);
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