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
//getting server ip
$server_ip= gethostbyname(gethostname());
//creating upload url
$mkdir_url='http://'.$server_ip.'/kazi_project/android/res';

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
if (isset($_POST['email']) && isset($_POST['userid']))
    {
		//store the posted values in variables
		$email = $_POST['email'];
		$userid = $_POST['userid'];
		
		//get current date
		$today= date("d-m-Y H:i");
		
		$sql_command="SELECT *FROM contractors_accounts WHERE emails = '$email'";
		$result = mysqli_query($db::$connection,$sql_command) ;
		if (!empty($result))
        {
			// check for empty result
			if (mysqli_num_rows($result) > 0) 
			{
				$result = mysqli_fetch_array($result); 
				$companyId=$result['id'];
				//get the table name
				$head=makeTableName((string)$companyId);
				$tablename=$head.'_personnel_matrix';
				//check if the userid is already registered
				$sql_command_check="SELECT *FROM $tablename WHERE userid = '$userid'";
				$result_check = mysqli_query($db::$connection,$sql_command_check) ;
				if(!$result_check || mysqli_num_rows($result_check)>0)
				{
					// failed to insert row
					$response["success"] = -5;
					$response["message"] = "userid already there.". mysqli_error($db::$connection);
					echo json_encode($response);
				}
				else
				{
					// mysql inserting a new row
					$sqlcommand_insert="INSERT INTO $tablename(dateadded,userid)"
						. " VALUES('$today','$userid')";
					$result_insert = mysqli_query($db::$connection,$sqlcommand_insert) ;
					if($result_insert)
					{
						 $sql_command="UPDATE users_accounts SET company='$companyId', datechanged='$today' WHERE id=$userid";
						 $result = mysqli_query($db::$connection,$sql_command) ; 
							// check if row updated or not 
							if ($result)
							{
								$response["success"] = 1;
								$response["message"] = "added";
								$response["id"]=$companyId;
								echo json_encode($response);
							} 
							else 
							{
								$response["success"] = -4;
								$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
								echo json_encode($response);
							}
					}
					else
					{
						$response["success"] = -3;
						$response["message"] = "row not inserted.". mysqli_error($db::$connection);
						echo json_encode($response);
					}
					
				}
				
            
            }
            else
            {
				// no account
				$response["success"] = -2;
				$response["message"] = "email not found". mysqli_error($db::$connection);
				echo json_encode($response);
            }
        }
    else 
        {
        // no account
        $response["success"] = -1;
        $response["message"] = "email not found". mysqli_error($db::$connection);
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