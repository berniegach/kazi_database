<?php
 
/*
 * Following code will update the seller account
 */
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();
// check for required fields
if (isset($_POST['email']) && isset($_POST['password']))
    {
    $email=$_POST['email'];
    $password=$_POST['password'];
    //hash the password
    $hashed_password= password_hash($password, PASSWORD_DEFAULT);
    //get current date
    $today= date("d-m-Y H:i");
    // mysql updating a new row
    $sql_command="UPDATE users_accounts SET passwords='$hashed_password',  datechanged='$today' WHERE emails='$email'";
    $result = mysqli_query($db::$connection,$sql_command) ; 
    // check if row updated or not 
    if ($result)
        {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "account successfully updated.";
        echo json_encode($response);
    } 
    else 
        {
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
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