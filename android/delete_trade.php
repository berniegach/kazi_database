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
if (isset($_POST['id']) && isset($_POST['tradename']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$tradename = $_POST['tradename'];
		
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_personnel_matrix';
		//get the lengths
		$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
		$result = mysqli_query($db::$connection,$sql_command) ;
		$result = mysqli_fetch_array($result);
		$lengths=$result['lengths'];
		//get individual column groups
		$lengths_pieces=explode(":",$lengths);
		$mand=explode(",",$lengths_pieces[0]);
		$jobs=explode(",",$lengths_pieces[1]);
		$trade=explode(",",$lengths_pieces[2]);
		//get tradename id
		$sql_command_id="SELECT *FROM $tablename WHERE $tradename='1'";
		$result_id = mysqli_query($db::$connection,$sql_command_id) ;
		$row= mysqli_fetch_array($result_id);
		$tradeid=$row['id'];
		//delete row
		$sql_command_delete="ALTER TABLE $tablename DROP COLUMN $tradename";
		$result_delete = mysqli_query($db::$connection,$sql_command_delete) ;
		if($result_delete)
		{
			$sql_command_delete_row ="DELETE FROM $tablename WHERE id = $tradeid";
			$result_delete_row=mysqli_query($db::$connection,$sql_command_delete_row) ;
			if($result_delete_row)
			{
				//shift ids
				$sql_command_shift="UPDATE $tablename SET id=id-1 WHERE id>$tradeid ";
				$result_shift = mysqli_query($db::$connection,$sql_command_shift) ;
				if($result_shift)
				{
					$new_lengths='m,'.$mand[1].':j,'.$jobs[1].':t,'.($trade[1]-1);
					$sql_command_update="UPDATE contractors_accounts SET lengths='$new_lengths', datechanged='$today' WHERE id=$id";
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
			}
			else
			{
				$response["success"] = -2;
				$response["message"] = "trade not deleted.". mysqli_error($db::$connection);
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
?>