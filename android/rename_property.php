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
//creating the upload url
////$upload_url=$_SERVER['DOCUMENT_ROOT'].'/kazi_project/android/src/contractors/';
// check for required fields
if (isset($_POST['id']) && isset($_POST['propertyname']) && isset($_POST['newpropertyname']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$propertyname = $_POST['propertyname'];
		$newpropertyname=$_POST['newpropertyname'];
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_equipments_matrix';
		////$tablenamecertificates=$head.'_certificates';
		//check if the column is already registered
		//NOTE the tablename is not in quotes and the column is in quotes coz...
        $sql_command_check="SHOW COLUMNS FROM $tablename LIKE '$newpropertyname'";
		$result_check = mysqli_query($db::$connection,$sql_command_check) ;
		if(!$result_check || mysqli_num_rows($result_check)>0)
			{
				// failed to insert column
				$response["success"] = -1;
				$response["message"] = "column already there.". mysqli_error($db::$connection);
				echo json_encode($response); 
			}
			else
			{
				$sql_command_add="ALTER TABLE $tablename CHANGE $propertyname $newpropertyname TEXT";
				$result = mysqli_query($db::$connection,$sql_command_add) ;
				if($result)
				{
					$response["success"] = 1;
					$response["message"] = "successful";
					//get the certificates
					/*$sqlcommand_certs="SELECT *FROM $tablenamecertificates WHERE whereis = '$qualificationname'";
					$result_certs = mysqli_query($db::$connection,$sqlcommand_certs) ;
					if(!empty($result_certs) || mysqli_num_rows($result_certs) > 0)
					{
						while ($row_certs= mysqli_fetch_array($result_certs))
						{
							$userid=$row_certs['userid'];
							//update the record
							$sqlcommand_update="UPDATE $tablenamecertificates SET whereis='$newqualificationname' WHERE userid='$userid'";
							$result_update = mysqli_query($db::$connection,$sqlcommand_update) ;
							if($result_update)
							{
								//rename the certificate in server
								$userfoldername=makeTableName((string)$userid);
								$file_url=$upload_url.$head.'/certificates/'.$userfoldername.'/'.$qualificationname.'.jpg';
								$new_file_url=$upload_url.$head.'/certificates/'.$userfoldername.'/'.$newqualificationname.'.jpg';
								if(!rename($file_url,$new_file_url))
								{
									// failed to insert column
									$response["success"] = -5;
									$response["message"] = $response["message"]."  :failed rename in server". mysqli_error($db::$connection);
								}
							}
							else
							{
								// failed to insert column
								$response["success"] = -4;
								$response["message"] = $response["message"]."  :failed to update". mysqli_error($db::$connection);
							}
						}
					}
					else
					{
						// failed to insert column
						$response["success"] = -3;
						$response["message"] = "no certs.". mysqli_error($db::$connection);
					}*/
					echo json_encode($response);
				}
				else
				{
					// failed to insert column
					$response["success"] = -2;
					$response["message"] = "error.". mysqli_error($db::$connection);
					echo json_encode($response);
				}
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