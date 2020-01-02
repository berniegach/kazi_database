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
		//array containing expanded permissions
		$data=array();
		//store the posted values in variables
		$permissions=$_POST['permissions'];
		//get individual permissions
		$permission_pieces=explode(":",$permissions);
		//loop through the pieces 
		for($c=0; $c<count($permission_pieces)-1; $c++)
		{
			/* the format follows the following
			* 1. 0/1  0 for permissions given 1 for permissions to others
            * 2. C/U  C for contractor , U for general user
            * 3. E/V/A  E for edit tasks, V for view compliance,  A for add data
            * 4  the persons id*/
			$data_piece=explode(",",$permission_pieces[$c]);
			if($data_piece[1]=='C')
			{
				$id=$data_piece[3];
				$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
				$result = mysqli_query($db::$connection,$sql_command) ;
				if(!empty($result))
				{
					//result not empty
					if(mysqli_num_rows($result) > 0)
					{
						$result = mysqli_fetch_array($result); 
						//info array
						$info=array();
						$info['given']=$data_piece[0];
						$info['persona']=$data_piece[1];
					    $info['type']=$data_piece[2];
						$info['id']=$result['id'];
						$info['email']=$result['emails'];
						$info['username']=$result['usernames'];
						$info['company']="*";
						$info['position']="*";
                        //push the data into the final array
						array_push($data,$info);
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
			else if($data_piece[1]=='U')
			{
				$id=$data_piece[3];
				$sql_command="SELECT *FROM users_accounts WHERE id = '$id'";
				$result = mysqli_query($db::$connection,$sql_command) ;
				if(!empty($result))
				{
					//result not empty
					if(mysqli_num_rows($result) > 0)
					{
						$result = mysqli_fetch_array($result); 
						//info array
						$info=array();
						$info['given']=$data_piece[0];
						$info['persona']=$data_piece[1];
					    $info['type']=$data_piece[2];
						$info['id']=$result['id'];
						$info['email']=$result['emails'];
						$info['username']=$result['usernames'];
						$info['company']=$result['company'];
						$info['position']="*";
                        //push the data into the final array
						array_push($data,$info);
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
		//response
		$response["success"] = 1;
		$response["data"] = array();
		array_push($response["data"], $data); 
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