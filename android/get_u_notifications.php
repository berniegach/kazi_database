<?php
 
/*
 * Following code will get the user notifications
 * The returned infos are id, classes, messages, dateadded.
 * Arguments are:
 * id==user id.
 * Returns are:
 * success==1 successful get
 * success==0 for id argument missing
 */
 
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// connecting to db
$db = new DB_CONNECT();
 
// check for required fields
if (isset($_POST['id']))
    {
		$id = $_POST['id']; 
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_u_notifications';
		$sql_command="SELECT *FROM $tablename";
		$result = mysqli_query($db::$connection,$sql_command) ;     
		if(!empty($result) || mysqli_num_rows($result_check)>0)
		{
			$response["notis"]=array();
			while($row= mysqli_fetch_array($result))
			{
				$notis=array();
				$notis["id"]=$row['id'];
				$notis["classes"]=$row['classes'];
				$notis["messages"]=$row['messages'];
				$notis["dateadded"]=$row['dateadded'];
				array_push($response["notis"], $notis);
			}
			$response["success"] = 1; 
            $response["message"] = "found them";
            echo json_encode($response);
		}
		else
		{
			$response["success"] = 0; 
            $response["message"] = "no notis ".mysqli_error($db::$connection);
            echo json_encode($response);
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