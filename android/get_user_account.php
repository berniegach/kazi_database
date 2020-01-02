<?php
/*
 * Following code will get single contractor account
 */
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();
// check for post data
if (isset($_POST["email"]) &&isset($_POST["password"]))
    {
    $email = $_POST['email'];
    $password=$_POST["password"];
    // get an account
    $sql_command="SELECT *FROM users_accounts WHERE emails = '$email'";
    $result = mysqli_query($db::$connection,$sql_command) ;
    if (!empty($result))
        {
        // check for empty result
        if (mysqli_num_rows($result) > 0) 
            {
            $result = mysqli_fetch_array($result); 
            $hashed_password=$result['passwords'];
            //if password match
            if(password_verify($password, $hashed_password))
            {
                //account array
                $account=array();
                $account['id']=$result['id'];
                $account['email']=$result['emails'];
                $account['password']=$password;
                $account['username']=$result['usernames'];
				$account['company']=$result['company'];
				$account['position']=$result['position'];
				$account['country']=$result['country'];
                $account['location']=$result['location'];
				$account['permissions']=$result['permissions'];
				$account['notifications']=$result['notifications'];
				$account['notificationsequipments']=$result['notificationsequipments'];
                $account['dateadded']=$result['dateadded'];
                $account['datechanged']=$result['datechanged'];
                //response
                $response["success"] = 1;
                $response["account"] = array();
                array_push($response["account"], $account); 
                echo json_encode($response);
            }
            else 
                {
                $response["success"] = -1;
                $response["message"] = "password ". mysqli_error($db::$connection);
                echo json_encode($response);
                }
            }
            else
                {
					 // no account
					$response["success"] = -2;
					$response["message"] = "no username". mysqli_error($db::$connection);
					echo json_encode($response);
                }
    }
    else 
        {
        // no account
        $response["success"] = -2;
        $response["message"] = "no username". mysqli_error($db::$connection);
        echo json_encode($response);
        }
}
else
    {
    // required field is missing
    $response["success"] = -3;
    $response["message"] = "Required field(s) is missing". mysqli_error($db::$connection);
    echo json_encode($response);
    }
?>