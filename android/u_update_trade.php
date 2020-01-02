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
if (isset($_POST['id']) && isset($_POST['tradename']) && isset($_POST['newtradename']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$userid=$_POST['userid'];
		$tradename = $_POST['tradename'];
		$newtradename=$_POST['newtradename'];
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_personnel_matrix';
		//update
		if($tradename=="null")
			$sql_command="UPDATE $tablename SET $newtradename='1', datechanged='$today' WHERE userid='$userid'";
		else
			$sql_command="UPDATE $tablename SET $tradename='0', $newtradename='1', datechanged='$today' WHERE userid='$userid'";
		$sql_command2="UPDATE users_accounts SET position='$newtradename', datechanged='$today' WHERE id=$userid";
        $result=mysqli_query($db::$connection,$sql_command) ;
		$result2 = mysqli_query($db::$connection,$sql_command2) ;
		if($result && $result2)
		{
			// failed to insert column
			$response["success"] = 1;
			$response["message"] = "success";
			echo json_encode($response);
		}
		else
		{
			// failed to insert column
			$response["success"] = -1;
			$response["message"] = "error.". mysqli_error($db::$connection);
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
?>