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
//creating the upload url
$upload_url=$_SERVER['DOCUMENT_ROOT'].'/kazi_project/android/src/contractors/';

// check for required fields
if (isset($_POST['id']) && isset($_POST['userid']) && isset($_POST['equipment']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$userid = $_POST['userid'];
		$equipment=$_POST['equipment'];
		
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_equipments_matrix';
		$ufolder=makeTableName((string)$userid);
		$upload_url=$upload_url.$head.'/equipmentscertificates/'.$ufolder;
		if(!file_exists($upload_url))
			mkdir($upload_url,0777,true);
		//check if the email is already registered
		$sql_command_check="SELECT *FROM $tablename WHERE userid = '$userid' AND $equipment='1'";
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
			// mysql inserting a new row
			$sqlcommand_insert="INSERT INTO $tablename(userid, $equipment, dateadded)"
				. " VALUES($userid,'1','$today')";
			$result_insert = mysqli_query($db::$connection,$sqlcommand_insert);
			if($result_insert)
			{
				// failed to insert column
				$response["success"] = 1; 
				$response["message"] = "success";
				echo json_encode($response);
			}
			else
			{
				// failed to insert column
				$response["success"] = -2;
				$response["message"] = "error inserting.". mysqli_error($db::$connection);
				echo json_encode($response);
			}
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