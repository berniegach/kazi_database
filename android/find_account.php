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
if (isset($_POST['persona']) && isset($_POST['manner']) && isset($_POST['email']) && isset($_POST['id']))
    {
		//store the posted values in variables
		$persona = $_POST['persona'];
		$manner = $_POST['manner'];
		$email = $_POST['email'];
		$id = $_POST['id'];
		//check if the person is a contractor or a general user
		if($persona==0)
		{
			//contractor
			//check if we are using email or id 
			if($manner==0)
			{
				//email
				//find the account
				$sql_command_check="SELECT *FROM contractors_accounts WHERE emails = '$email'";
				$result = mysqli_query($db::$connection,$sql_command_check) ;
				if(!empty($result))
				{
					//result not empty
					if(mysqli_num_rows($result) > 0)
					{
						$result = mysqli_fetch_array($result); 
						//info array
						$info=array();
						$info['id']=$result['id'];
						$info['email']=$result['emails'];
						$info['username']=$result['usernames'];
						$info['company']="*";
						$info['position']="*";
						//response
						$response["success"] = 1;
						$response["info"] = array();
						array_push($response["info"], $info); 
						echo json_encode($response);
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username rows1". mysqli_error($db::$connection);
						echo json_encode($response);
					}
				}
				else
				{
					 // no account
					$response["success"] = -1;
					$response["message"] = "no username empty1". mysqli_error($db::$connection);
					echo json_encode($response);
				}
			}
			else
			{
				//id
				//find the account
				$sql_command_check="SELECT *FROM contractors_accounts WHERE id = '$id'";
				$result = mysqli_query($db::$connection,$sql_command_check) ;
				if(!empty($result))
				{
					//result not empty
					if(mysqli_num_rows($result) > 0)
					{
						$result = mysqli_fetch_array($result); 
						//info array
						$info=array();
						$info['id']=$result['id'];
						$info['email']=$result['emails'];
						$info['username']=$result['usernames'];
						$info['company']="*";
						$info['position']="*";
						//response
						$response["success"] = 1;
						$response["info"] = array();
						array_push($response["info"], $info); 
						echo json_encode($response);
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username rows2". mysqli_error($db::$connection);
						echo json_encode($response);
					}
				}
				else
				{
					 // no account
					$response["success"] = -1;
					$response["message"] = "no username empty2". mysqli_error($db::$connection);
					echo json_encode($response);
				}
				
			}
		}
		else
		{
			//general user
			//check if we are using email or id 
			if($manner==0)
			{
				//email
				//find the account
				$sql_command_check="SELECT *FROM users_accounts WHERE emails = '$email'";
				$result = mysqli_query($db::$connection,$sql_command_check) ;
				if(!empty($result))
				{
					//result not empty
					if(mysqli_num_rows($result) > 0)
					{
						$result = mysqli_fetch_array($result); 
						//info array
						$info=array();
						$info['id']=$result['id'];
						$info['email']=$result['emails'];
						$info['username']=$result['usernames'];
						$info['company']=$result['company'];
						$info['position']="*";
						//response
						$response["success"] = 1;
						$response["info"] = array();
						array_push($response["info"], $info); 
						echo json_encode($response);
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username rows11". mysqli_error($db::$connection);
						echo json_encode($response);
					}
				}
				else
				{
					 // no account
					$response["success"] = -1;
					$response["message"] = "no username empty11". mysqli_error($db::$connection);
					echo json_encode($response);
				}
			}
			else
			{
				//id
				//find the account
				$sql_command_check="SELECT *FROM users_accounts WHERE id = '$id'";
				$result = mysqli_query($db::$connection,$sql_command_check) ;
				if(!empty($result))
				{
					//result not empty
					if(mysqli_num_rows($result) > 0)
					{
						$result = mysqli_fetch_array($result); 
						//info array
						$info=array();
						$info['id']=$result['id'];
						$info['email']=$result['emails'];
						$info['username']=$result['usernames'];
						$info['company']=$result['company'];
						$info['position']="*";
						//response
						$response["success"] = 1;
						$response["info"] = array();
						array_push($response["info"], $info); 
						echo json_encode($response);
					}
					else
					{
						 // no account
						$response["success"] = -1;
						$response["message"] = "no username rows22". mysqli_error($db::$connection);
						echo json_encode($response);
					}
				}
				else
				{
					 // no account
					$response["success"] = -1;
					$response["message"] = "no username empty22". mysqli_error($db::$connection);
					echo json_encode($response);
				}
				
			}
		}
		
  
			
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