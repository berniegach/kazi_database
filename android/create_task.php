<?php
 
/*
 * Following code will create a new task row 
 * The returned columns are 
 * Arguments are:
 * id, title, description, starting, ending, repeatition, location, position.
 * Returns are:
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


// check for required fields
if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['starting']) && isset($_POST['ending']) && isset($_POST['repetition']) && isset($_POST['location']) && isset($_POST['position']) &&isset($_POST['geofence']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$title = $_POST['title'];
		$description=$_POST['description'];
		$starting=$_POST['starting'];
		$ending=$_POST['ending'];
		$repetition=$_POST['repetition'];
		$location=$_POST['location'];
		$position=$_POST['position'];
		$geofence=$_POST['geofence'];
		
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_tasks';
		
		// mysql inserting a new row
		$sqlcommand_insert="INSERT INTO $tablename(titles, descriptions, startings, endings, repetitions, locations, positions, geofence ,dateadded)"
			. " VALUES('$title','$description', '$starting', '$ending', '$repetition', '$location', '$position', '$geofence', '$today')";
		$result_insert = mysqli_query($db::$connection,$sqlcommand_insert) ;
		if($result_insert)
		{
			$last_id= mysqli_insert_id($db::$connection);
			$response["success"] = 1;
			$response["id"]=$last_id;
			$response["dateadded"]=$today;
			$response["message"] = "added";
			echo json_encode($response);
		}
		else
		{
			$response["success"] = -2;
			$response["message"] = "row not inserted.". mysqli_error($db::$connection);
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