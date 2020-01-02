<?php
 
/*
 * Following code will upload the profile pic and store them in /profile_pics
 *
 */
require_once __DIR__ . '/db_connect.php';
//getting server ip
$server_ip= gethostbyname(gethostname());
//creating upload url
$upload_url=$_SERVER['DOCUMENT_ROOT'].'/kazi_project/android/src/contractors/';
//response array
$response=array();
// connecting to db
$db = new DB_CONNECT();
if(isset($_POST['name']) && isset($_FILES['jpg']['name'])  && isset($_POST['id']))
{
    //getting name from the request
    $name=$_POST['name'];
	$id=$_POST['id'];
	//folders
	$cfolder=makeTableName((string)$id);
	$upload_url=$upload_url.$cfolder.'/profilepics/';
    //getting file info
    $fileinfo= pathinfo($_FILES['jpg']['name']);
    //getting the file extension
    $extension=$fileinfo['extension'];
    //file path to store in the server
    $file_path=$upload_url.$name.'.'.$extension;
	if(file_exists($file_path))
				unlink($file_path);
    //try saving the file
    try {
        //saving the file
		if(move_uploaded_file($_FILES['jpg']['tmp_name'],$file_path))
		{
			$response['message']="sucessful";
			//change file permission
			chmod($file_path,0777);
		}
		else
		{
			$response['error']='error '.$_FILES['jpg']['error'];
			$response['message']='there was an error';
		}
        
    } catch (Exception $ex) {
        $response['error']=true;
        $response['message']=$e->getMessage();
        echo json_encode($response);
    }
}
 else {
    $response['error']=true;
    $response['message']="please choose a file";
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