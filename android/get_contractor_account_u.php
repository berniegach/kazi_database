<?php
/*
 * Following code will get single contractor account details for the user. The following boss account details are returned
 * id,email,username, country, location, permissions, lengths, notifications, dateadded, datechanged
 * Arguments are:
 * id==boss id.
 * Returns are:
 * success==1 successful get
 * sucess==-2 for no account with that id found
 * success==-3 for id argument missing
 */
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();
// check for post data
if (isset($_POST["id"]) )
    {
    $id = $_POST['id'];
    // get an account
    $sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
    $result = mysqli_query($db::$connection,$sql_command) ;
    if (!empty($result))
        {
        // check for empty result
        if (mysqli_num_rows($result) > 0) 
            {
            $result = mysqli_fetch_array($result); 
            //account array
            $account=array();
            $account['id']=$result['id'];
            $account['email']=$result['emails'];
            $account['username']=$result['usernames'];
			$account['country']=$result['country'];
            $account['location']=$result['location'];
			$account['permissions']=$result['permissions'];
			$account['lengths']=$result['lengths'];
			$account['lengthsequipments']=$result['lengthsequipments'];
			$account['notifications']=$result['notifications'];
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