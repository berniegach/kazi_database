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

// check for required fields
if (isset($_POST['id']) && isset($_POST['tradename']) && isset($_POST['newtradename']))
    {
		//store the posted values in variables
		$id = $_POST['id'];
		$tradename = $_POST['tradename'];
		$newtradename=$_POST['newtradename'];
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_personnel_matrix';
		//check if the column is already registered
		//NOTE the tablename is not in quotes and the column is in quotes coz...
        $sql_command_check="SHOW COLUMNS FROM $tablename LIKE '$newtradename'";
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
				$sql_command_add="ALTER TABLE $tablename CHANGE $tradename $newtradename TEXT";
				$result = mysqli_query($db::$connection,$sql_command_add) ;
				if($result)
				{
					$response["success"] = 1;
					$response["message"] = "success";
					//get the users accounts with the tradename
					$greater=0;
					$sqlcommand_users="SELECT *FROM $tablename WHERE userid > $greater";
					$result_users = mysqli_query($db::$connection,$sqlcommand_users) ;
					if(!empty($result_users) || mysqli_num_rows($result_users) > 0)
					{
						while ($row_users= mysqli_fetch_array($result_users))
						{
							$userid=$row_users['userid'];
							//find the users int users_accounts
							$sql_command_one_user="SELECT *FROM users_accounts WHERE id = '$userid'";
							$result_one_user = mysqli_query($db::$connection,$sql_command_one_user) ;
							if(!empty($result_one_user) || mysqli_num_rows($result_one_user) > 0)
							{
								$row_one_user=mysqli_fetch_array($result_one_user);
								$position=$row_one_user['position'];
								if($position==$tradename)
								{
									//update the record
									$sqlcommand_update="UPDATE users_accounts SET position='$newtradename' WHERE id='$userid'";
									$result_update = mysqli_query($db::$connection,$sqlcommand_update) ;
									if(!$result_update)
									{
										// failed to insert column
										$response["success"] = -4;
										$response["message"] = $response["message"]."  :failed to find users". mysqli_error($db::$connection);
									}
								}
							}
							else
							{
								// failed to insert column
								$response["success"] = -4;
								$response["message"] = $response["message"]."  :failed to find users". mysqli_error($db::$connection);
							}
						}
					}	
					else
					{
						// failed to insert column
						$response["success"] = -3;
						$response["message"] = $response["message"]."  :failed to find users". mysqli_error($db::$connection);
					}					
					
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