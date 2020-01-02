<?php
 
/*
 * Following code will get all the rows in the boss personnel matrix table.
 * The columns returned are from index 2 marking the first requirement to the last, position.
 * The rows returned are from index 1 to the last. From the position definitions to all the personnel requirements
 * Arguments are:
 * id==boss id.
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
    $id = $_POST['id']; 
	//get the table name
	$head=makeTableName((string)$id);
	$tablename=$head.'_equipments_matrix';
	//get the lengths, how many positions and qualifications
	$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
	$result = mysqli_query($db::$connection,$sql_command) ;
	$result = mysqli_fetch_array($result);
	$lengths=$result['lengthsequipments'];
	//get individual column groups
	$lengths_pieces=explode(":",$lengths);
	//we only need the trades count
	//$mand=explode(",",$lengths_pieces[0]);
	//$jobs=explode(",",$lengths_pieces[1]);
	$property=explode(",",$lengths_pieces[2]);	
	$property_ids=array();
	$sql_command="SELECT id FROM $tablename";
	$result=mysqli_query($db::$connection,$sql_command);
	$count=0;
		 if (!empty($result))
        {
        // check for empty result
        if (mysqli_num_rows($result) > 0) 
            {
            while ($row= mysqli_fetch_array($result))
            {
                if($count>=$property)
				break;
			$property_ids[]=$row['id'];
			$count++;
                
            }
            $response["success"] = 1; 
            $response["message"] = "found them";
			$response["ids"]=array();
            array_push($response["ids"], $property_ids);
            echo json_encode($response);

        }
        else
            {
            // no product found
            $response["success"] = 0;
            $response["message"] = "no ids". mysqli_error($db::$connection);
            echo json_encode($response);
        }
    }
    else 
        {
        // no product found
        $response["success"] = 0;
        $response["message"] = "no ids found". mysqli_error($db::$connection);
       // echo no users JSON
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
?>