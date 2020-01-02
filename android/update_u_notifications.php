<?php
/*
 * Following code will update notification period ettings for the user for their requirements
 * Arguments are:
 * id==user id.
 * notifications= the string containing new periods
 * Returns are:
 * success==1 successful get
 * success==-1 for id and notifications argument missing
 * success=-2 for an update error
 */
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();
// check for required fields
if (isset($_POST['id']) && isset($_POST['notifications']))
    {
    $id=$_POST['id'];
    $notifications=$_POST['notifications'];
    //get current date
    $today= date("d-m-Y H:i");
    // mysql updating a new row
    $sql_command="UPDATE users_accounts SET  notifications='$notifications', datechanged='$today' WHERE id=$id";
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
        $response["success"] = -2;
        $response["message"] = "Oops! An error occurred.". mysqli_error($db::$connection);
        echo json_encode($response);
        }
}
else 
    {
    // required field is missing
    $response["success"] = -1;
    $response["message"] = "Required field(s) is missing";
     echo json_encode($response);
    }
?>