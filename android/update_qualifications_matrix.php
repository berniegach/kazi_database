<?php
 
/*
 * Following code will update a product information
 * A product is identified by product id (pid)
 */
 
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// connecting to db
$db = new DB_CONNECT();
 
// check for required fields
if (isset($_POST['sqlcommand'])) {
 
    $sqlcommand = $_POST['sqlcommand'];
    $result = mysqli_query($db::$connection,$sqlcommand) ;
 
    // check if row updated or not
    if ($result) {
        // successfully updated
        $response["success"] = 1;
        $response["message"] = "successfully updated.";
 
        // echoing JSON response
        echo json_encode($response);
    }
    else
        {
     $response["success"] = 0;
     $response["message"] = "updating error". mysqli_error($db::$connection);
 
    // echoing JSON response
    echo json_encode($response);
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
    echo json_encode($response);
}
?>