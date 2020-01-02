<?php
 
/*
 * Following code will update a single task row 
 */
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();
// check for required fields
if (isset($_POST['bossid']) &&isset($_POST['id'])  && isset($_POST['classes']) && isset($_POST['reviewer']) && isset($_POST['review']) && isset($_POST['toimprove']) && isset($_POST['rating']))
    {
        //store the posted values in variables
		$bossid = $_POST['bossid'];
		$id = $_POST['id'];
		//$userid = $_POST['userid'];
		$classes=$_POST['classes'];
		$reviewer=$_POST['reviewer'];
		$review=$_POST['review'];
		$toimprove=$_POST['toimprove'];
		$rating=$_POST['rating'];
		//$themonth=$_POST['themonth'];
		//$theyear=$_POST['theyear'];
    
    //get current date
    $today= date("d-m-Y H:i");
	//get the table name
	$head=makeTableName((string)$bossid);
	$tablename=$head.'_performance_review';
	// mysql updating a new row
	$sql_command="UPDATE $tablename SET classes='$classes', reviewer='$reviewer', review='$review', toimprove='$toimprove',rating='$rating', datechanged='$today' WHERE id=$id";
	$result = mysqli_query($db::$connection,$sql_command) ; 
	// check if row updated or not 
	if ($result)
		{
		    // successfully inserted into database
			$response["success"] = 1;
			$response["message"] = "successfully updated.";
			echo json_encode($response);
		} 
	else 
	{
    	$response["success"] = 0;
		$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
		echo json_encode($response);
	}
}
else 
    {
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