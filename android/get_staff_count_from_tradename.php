<?php
 
/*
 * get staff account id in the account table from the schema table
 */
// include db connect class
require_once __DIR__.'/db_connect.php';
//connecting to db-connect class
$db=new DB_CONNECT();
// array for JSON response
$response = array();
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
    $id=$_POST['id'];
    $tradename=$_POST['tradename'];
	//get the table name
	$head=makeTableName((string)$id);
	$tablename=$head.'_personnel_matrix';

    $sqlcommand="SELECT *FROM $tablename WHERE $tradename='1'";
    $result = mysqli_query($db::$connection,$sqlcommand) ;
    if (!empty($result))
        {
          $staffcount=mysqli_num_rows($result);
          $response["success"] = 1; 
          $response["staffcount"]=$staffcount-1;
          echo json_encode($response);       
       
    }
    else 
        {
        // no product found
        $response["success"] = -2;
        $response["message"] = "some error". mysqli_error($db::$connection);
        echo json_encode($response);
    }
 
} else {
    // required field is missing
    $response["success"] = -1;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
     echo json_encode($response);
}
?>