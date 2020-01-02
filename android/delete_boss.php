<?php
 
/*
 * Following code will delete a boss  from the system.
 * the first is to remove the folders belonging to the boss. folder name is boss's id
 * second is to delete the tables personnel,eqquipments, certificates, equipmentscertificates, notifications and tasks
 * third is to delete the boss's account info from contractors account
 * All product details are read from HTTP Post Request
 */
 // array for JSON response
$response = array();
$response["message"]="";
// include db connect class
require_once __DIR__ . '/db_connect.php';
// connecting to db
$db = new DB_CONNECT();

// check for required fields
if (isset($_POST['id']) )
    {
		//store the posted values in variables
		$id = $_POST['id'];
		
		//get current date
		$today= date("d-m-Y H:i");
		//get the table name
		$head=makeTableName((string)$id);
		$tablename1=$head.'_personnel_matrix';
		$tablename2=$head.'_equipments_matrix';
		$tablename3=$head.'_notifications';
		$tablename4=$head.'_tasks';
		$tablename5=$head.'_certificates';
		$tablename6=$head.'_certificates_equipments';
		$tablename7=$head.'_performance_review';
		//user's own folder
		$folder='src/contractors/'.$head.'/';
		//delete the folder
		deleteDir($folder);
		
		//delete the tables personnel,eqquipments, certificates, equipmentscertificates, notifications and tasks
		$sql_command_delete_table1 ="DROP TABLE $tablename1";
		$sql_command_delete_table2 ="DROP TABLE $tablename2";
		$sql_command_delete_table3 ="DROP TABLE $tablename3";
		$sql_command_delete_table4 ="DROP TABLE $tablename4";
		$sql_command_delete_table5 ="DROP TABLE $tablename5";
		$sql_command_delete_table6 ="DROP TABLE $tablename6";
		$sql_command_delete_table7 ="DROP TABLE $tablename7";
		$result_delete_table1=mysqli_query($db::$connection,$sql_command_delete_table1) ;
		$result_delete_table2=mysqli_query($db::$connection,$sql_command_delete_table2) ;
		$result_delete_table3=mysqli_query($db::$connection,$sql_command_delete_table3) ;
		$result_delete_table4=mysqli_query($db::$connection,$sql_command_delete_table4) ;
		$result_delete_table5=mysqli_query($db::$connection,$sql_command_delete_table5) ;
		$result_delete_table6=mysqli_query($db::$connection,$sql_command_delete_table6) ;
		$result_delete_table7=mysqli_query($db::$connection,$sql_command_delete_table7) ;
		if(!($result_delete_table1 && $result_delete_table2 && $result_delete_table3 && $result_delete_table4 && $result_delete_table5 && $result_delete_table6 && $result_delete_table7))
		{
			$response["message"] .= "error deleting rows". mysqli_error($db::$connection);
		}
		
		//delete boss's info from contractors account table
		$sql_command_delete_row5 ="DELETE FROM contractors_accounts WHERE id = $id";
		$result_delete_row5=mysqli_query($db::$connection,$sql_command_delete_row5) ;
		if(!$result_delete_row5)
		{
			$response["message"] .= "error deleting boss's account info.". mysqli_error($db::$connection);
		}
		
		$response["success"] = 1;
		echo json_encode($response);
			
	} 
		

 else
	 {
    // required field is missing
    $response["success"] = -1;
    $response["message"] = "Required field(s) is missing";
     echo json_encode($response);
	}
	//delete the directory and its contents
	function deleteDir($dirPath)
	{
		if(!is_dir($dirPath))
			return;
		//if the last character is not a slash, add it for reasons
		if(substr($dirPath,strlen($dirPath)-1,1) != '/')
		{
			$dirPath.='/';
		}
		//search for all the path names of the folder contents. GLOB_MARK means attach slash to the end of the path name. Return as array
		$files=glob($dirPath.'*',GLOB_MARK);
		foreach($files as $file)
		{
			//loop through the file paths and if its is a folder . carry out the outlayed procedure
			if(is_dir($file))
			{
				deleteDir($file);
			}
			else
			{
				//delete the file
				unlink($file);
			}
		}
		//delete the folder
		rmdir($dirPath);
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