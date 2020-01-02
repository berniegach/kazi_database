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
if (isset($_POST["email"]))
    {
    $email = $_POST['email'];
    // get an account
    $sql_command="SELECT *FROM contractors_accounts WHERE emails = '$email'";
    $result = mysqli_query($db::$connection,$sql_command) ;
    if (!empty($result))
        {
        // check for empty result
        if (mysqli_num_rows($result) > 0) 
            {
            $response["success"] = 1;
			$response["message"] = "sucess";
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