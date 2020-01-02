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
if (isset($_POST['permissions']))
    {
		//store the posted values in variables
		$permissions=$_POST['permissions'];
		//get individual permissions
		$permission_pieces=explode(":",$permissions);
		//loop through the pieces 
		for($c=0; $c<count($permission_pieces)-1; $c++)
		{
			/* the format follows the following
			* 1. 0/1  0 for permissions remove 1 for permissions add
            * 2. C/U  C for contractor , U for general user
            * 3. E/V/A  E for edit tasks, V for view compliance,  A for add data
            * 4  the persons id*/
			$data_piece=explode(",",$permission_pieces[$c]);
			if($data_piece[1]==0)
			{
				//removing permissions start
				if($data_piece[2]=='C')
				{
					//contractor start
					$id=$data_piece[4];
					$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
					$result = mysqli_query($db::$connection,$sql_command) ;
					//start of empty
					if(!empty($result))
					{
						//start of not empty
						if(mysqli_num_rows($result) > 0)
						{
							$result = mysqli_fetch_array($result);
							$permits=$result['permissions'];
							$permits= str_replace('0'.','.$data_piece[2].','.$data_piece[3].','.$data_piece[0].':', "", $permits);
                            $permits= trim($permits);
							$sql_command_update="UPDATE contractors_accounts SET permissions='$permits'  WHERE id=$id";
							$result_update = mysqli_query($db::$connection,$sql_command_update) ;
							if(!$result_update)
							{
								$response["success"] = 0;
								$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
								echo json_encode($response);
							}
							else
							{
								//response
								$response["success"] = 1;
								$response["message"] = "permissions removed from contractor";
								echo json_encode($response);
							}
							
						}
						//end of not empty
						else
						{
							 // no account
							$response["success"] = -1;
							$response["message"] = "no username rows1". mysqli_error($db::$connection);
							echo json_encode($response);
						}
						//end of not empty
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username empty1". mysqli_error($db::$connection);
						echo json_encode($response);
					}
					//end of empty
				}
				else
				{
					//general user start
					$id=$data_piece[4];
					$sql_command="SELECT *FROM users_accounts WHERE id = '$id'";
					$result = mysqli_query($db::$connection,$sql_command) ;
					//start of empty
					if(!empty($result))
					{
						//start of not empty
						if(mysqli_num_rows($result) > 0)
						{
							$result = mysqli_fetch_array($result);
							$permits=$result['permissions'];
							$permits= str_replace('0'.','.$data_piece[2].','.$data_piece[3].','.$data_piece[0].':', "", $permits);
                            $permits= trim($permits);
							$sql_command_update="UPDATE users_accounts SET permissions='$permits'  WHERE id=$id";
							$result_update = mysqli_query($db::$connection,$sql_command_update) ;
							if(!$result_update)
							{
								$response["success"] = 0;
								$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
								echo json_encode($response);
							}
							else
							{
								//response
								$response["success"] = 1;
								$response["message"] = "permissions removed from user";
								echo json_encode($response);
							}
							
						}
						//end of not empty
						else
						{
							 // no account
							$response["success"] = -1;
							$response["message"] = "no username rows1". mysqli_error($db::$connection);
							echo json_encode($response);
						}
						//end of not empty
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username empty1". mysqli_error($db::$connection);
						echo json_encode($response);
					}
					//end of empty
				}
				//end of removing permissions
			}
			else
			{
				//adding permissions start
				if($data_piece[2]=='C')
				{
					//contractor start
					$id=$data_piece[4];
					$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
					$result = mysqli_query($db::$connection,$sql_command) ;
					//start of empty
					if(!empty($result))
					{
						//start of not empty
						if(mysqli_num_rows($result) > 0)
						{
							$result = mysqli_fetch_array($result);
							$permits=$result['permissions'];
							if($permits=="null")
								$permits="";
							$permits=$permits.'0,'.$data_piece[2].','.$data_piece[3].','.$data_piece[0].':';
							$sql_command_update="UPDATE contractors_accounts SET permissions='$permits'  WHERE id=$id";
							$result_update = mysqli_query($db::$connection,$sql_command_update) ;
							if(!$result_update)
							{
								$response["success"] = 0;
								$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
								echo json_encode($response);
							}
							
						}
						//end of not empty
						else
						{
							 // no account
							$response["success"] = -1;
							$response["message"] = "no username rows1". mysqli_error($db::$connection);
							echo json_encode($response);
						}
						//end of not empty
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username empty1". mysqli_error($db::$connection);
						echo json_encode($response);
					}
					//end of empty
				}
				else
				{
					//general user start
					$id=$data_piece[4];
					$sql_command="SELECT *FROM users_accounts WHERE id = '$id'";
					$result = mysqli_query($db::$connection,$sql_command) ;
					//start of empty
					if(!empty($result))
					{
						//start of not empty
						if(mysqli_num_rows($result) > 0)
						{
							$result = mysqli_fetch_array($result);
							$permits=$result['permissions'];
							if($permits=="null")
								$permits="";
							$permits=$permits.'0,'.$data_piece[2].','.$data_piece[3].','.$data_piece[0].':';
							$sql_command_update="UPDATE users_accounts SET permissions='$permits'  WHERE id=$id";
							$result_update = mysqli_query($db::$connection,$sql_command_update) ;
							if(!$result_update)
							{
								$response["success"] = 0;
								$response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
								echo json_encode($response);
							}
							
						}
						//end of not empty
						else
						{
							 // no account
							$response["success"] = -1;
							$response["message"] = "no username rows1". mysqli_error($db::$connection);
							echo json_encode($response);
						}
						//end of not empty
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username empty1". mysqli_error($db::$connection);
						echo json_encode($response);
					}
					//end of empty
				}
				//end of adding permissions
			}
			//end of script
			
			
			
		}
		//response
		$response["success"] = 1;
		$response["message"] = "permissions updated";
		echo json_encode($response);		
	} 
 else
	 {
    // required field is missing
    $response["success"] = -2;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
     echo json_encode($response);
	}
?>