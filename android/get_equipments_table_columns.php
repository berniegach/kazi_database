<?php
 
/**
 * Following code will get the boss equipments matrix table columns
 * the columns got start from index 2 all the way to the last equipment.
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
// check for required fields
if (isset($_POST['id']))
    {
    $id = $_POST['id']; 
	//get the table name
	$head=makeTableName((string)$id);
	$tablename=$head.'_equipments_matrix';
	//get the lengths
	$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
	$result = mysqli_query($db::$connection,$sql_command) ;
	$result = mysqli_fetch_array($result);
	$lengths=$result['lengthsequipments'];
	//get individual column groups
	$lengths_pieces=explode(":",$lengths);
	$mand=explode(",",$lengths_pieces[0]);
	$jobs=explode(",",$lengths_pieces[1]);
	$trade=explode(",",$lengths_pieces[2]);
	 //getting the table trade columns
	$columnnames=array();
	$sqlcolumns="SHOW COLUMNS FROM $tablename";    
	$resultcolumns = mysqli_query($db::$connection,$sqlcolumns) ;
	while ($row= mysqli_fetch_array($resultcolumns))
		{
			$columnnames[]=$row['Field'];
		}
    //add column identifiers
    $columns=array();
	for($c=2; $c<2+$mand[1]; $c++)
		$columns[]=$columnnames[$c].':m';
	for($c=2+$mand[1]; $c<2+$mand[1]+$jobs[1]; $c++)
		$columns[]=$columnnames[$c].':j';
	for($c=2+$mand[1]+$jobs[1]; $c<2+$mand[1]+$jobs[1]+$trade[1]; $c++)
		$columns[]=$columnnames[$c].':t';
    //success
    $response["success"] = 1;  
    $response["columns"]=array();
    array_push($response["columns"], $columns);
    echo json_encode($response);
       
   
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