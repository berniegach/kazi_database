<?php
 
 
/**
 * Following code will return a list of equipments.
 * The columns returned are from the first index of equipments to the last index of equipment
 * Arguments are:
 * id==boss id.
 * Returns are:
 * success==1 successful get
 * success==0 for id argument missing
 **/
 
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
if (isset($_POST['id']))
    {
    $id=$_POST['id']; 
    $equipmentsList=array();
    $equipmentsName="";
    $eCount;
	//get the table name
	$head=makeTableName((string)$id);
	$tablename=$head.'_equipments_matrix';
	
	//get the lengths
	$sql_command_length="SELECT *FROM contractors_accounts WHERE id = '$id'";
	$result_length = mysqli_query($db::$connection,$sql_command_length) ;
	$result_length = mysqli_fetch_array($result_length);
	$lengths=$result_length['lengthsequipments'];
	//get individual column groups
	$lengths_pieces=explode(":",$lengths);
	$eCount=explode(",",$lengths_pieces[2]);
    
     //getting the table trade columns
	$columnnames=array();
	$sqlcolumns="SHOW COLUMNS FROM $tablename";    
	$resultcolumns = mysqli_query($db::$connection,$sqlcolumns) ;
	$numColumns= mysqli_num_rows($resultcolumns);
	while ($row= mysqli_fetch_array($resultcolumns))
		{
			$columnnames[]=$row['Field'];
		}
	$start=count($columnnames)-(int)$eCount[1]-2;
	$end=count($columnnames)-2;
     for($c=$start; $c<$end; $c++)
    {
        $equipmentsList[]=$columnnames[$c];
    }
	//successful
	$response["success"] = 1; 
    $response["equipmentslist"]=array();
    array_push($response["equipmentslist"], $equipmentsList); 
	echo json_encode($response);
   
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
     echo json_encode($response);
}
?>