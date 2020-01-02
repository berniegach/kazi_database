<?php
 
/*
 * Following code will update or insert new certificate
 * information
 */
 
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();
 
// check for required fields
if (isset($_POST['id'])&&isset($_POST['userid']) && isset($_POST['whereis'])&&isset($_POST['issue']) && isset($_POST['expiry']))
{ 
    $id=$_POST['id'];
    $userid=$_POST['userid'];
    $whereis=$_POST['whereis'];
    $issue=$_POST['issue'];
    $expiry = $_POST['expiry'];
	//get the table name
	$head=makeTableName((string)$id);
	$tablename=$head.'_certificates_equipments';
	//get current date
	$today= date("d-m-Y H:i");
	//check if the userid is already registered
	$sql_command_check="SELECT *FROM $tablename WHERE userid = '$userid' AND whereis='$whereis'";
	$result_check = mysqli_query($db::$connection,$sql_command_check) ;
	if(!$result_check || mysqli_num_rows($result_check)>0)
	{
		// information already there therefore just update
		if($issue=='0')
			$sqlcommand_update="UPDATE $tablename SET expiry='$expiry', datechanged='$today' WHERE userid = '$userid' AND whereis='$whereis'";
		else
			$sqlcommand_update="UPDATE $tablename SET issue='$issue', datechanged='$today' WHERE userid = '$userid' AND whereis='$whereis'";
		$result_update = mysqli_query($db::$connection,$sqlcommand_update);
		if ($result_update)
        {
			// successfully updated
			$response["success"] = 1;
			$response["message"] = "successfully updated.";
			echo json_encode($response);
		}
		else
		{
			// failed to insert row
			$response["success"] = -1;
			$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
			echo json_encode($response);
		}
	}
	else
	{
		//certificate information not there threfore just insert one
		$sqlcommand_insert="INSERT INTO $tablename(userid, whereis, verified, issue, expiry, dateadded)"
			. " VALUES('$userid', '$whereis', '0', '$issue', '$expiry', '$today')";
		$result_insert = mysqli_query($db::$connection,$sqlcommand_insert) ;
		if ($result_insert)
        {
			// successfully updated
			$response["success"] = 1;
			$response["message"] = "successfully inserted.";
			echo json_encode($response);
		}
		else
		{
			// failed to insert row
			$response["success"] = -2;
			$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
			echo json_encode($response);
		}
	}
   
    if ($result)
        {
        // successfully updated
        $response["success"] = 1;
        $response["message"] = "successfully updated.";
        echo json_encode($response);
    }
    else
        {
		
    }
} else {
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