<?php
 
/**
 * Following code will a single personnel certificates info from boss certificates table.
 * The returned columns are id, userid, whereis, verified, issue, expiry, dateadded, datechanged.
 * Arguments are:
 * id==boss id.
 * userid== personnel id.
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
 
// check for post data
if (isset($_POST['id']) && isset($_POST['userid']))
    {
    $id = $_POST['id'];
	$userid=$_POST['userid'];
	//get the table name
	$head=makeTableName((string)$id);
	$tablename=$head.'_certificates';
 
    // get an account from staff accounts table
    $sqlcommand="SELECT *FROM $tablename WHERE userid = '$userid'";
     $result = mysqli_query($db::$connection,$sqlcommand) ;
 
    if (!empty($result))
        {
        // check for empty result
        if (mysqli_num_rows($result) > 0) 
            {
            //looping through all schemas
            $response["certs"]=array();
            while ($row= mysqli_fetch_array($result))
            {
                //temp certificates row
                  $certs=array();
                  $certs['id']=$row['id'];
                  $certs['userid']=$row['userid'];
                  $certs['whereis']=$row['whereis'];
                  $certs['verified']=$row['verified'];
                  $certs['issue']=$row['issue'];
                  $certs['expiry']=$row['expiry'];
                  $certs['dateadded']=$row['dateadded'];
				  $certs['datechanged']=$row['datechanged'];
                  //push a single schema into array
                  array_push($response["certs"], $certs); 
                
            }
            $response["success"] = 1; 
            $response["message"] = "found them";
            echo json_encode($response);

        }
        else
            {
            // no product found
            $response["success"] = 0;
            $response["message"] = "no certs". mysqli_error($db::$connection);
            echo json_encode($response);
        }
    }
    else 
        {
        // no product found
        $response["success"] = 0;
        $response["message"] = "no certs found". mysqli_error($db::$connection);
       // echo no users JSON
        echo json_encode($response);
    }
}
else {
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