<?php
 
/*
 * Following code will get the compliance data for all the one user
 * The returned infos are as contained in the result array
 * Arguments are:
 * id==boss id.
 * Returns are:
 * success==1 successful get
 * success==0 for id argument missing and errors
 */
 
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// connecting to db
$db = new DB_CONNECT();
 
// check for required fields
if (isset($_POST['id']) && isset($_POST['userid']))
    {
		$id = $_POST['id']; 
		$userid=$_POST['userid'];
		//get the table name
		$head=makeTableName((string)$id);
		$headuser=makeTableName((string)$userid);
		$tablename=$head.'_personnel_matrix';
		$certificatesTable=$head.'_certificates';
		//other variables
		$tradesList=array();
		$tradeName="";
		$tCount;
		$allcerts=array();
		$compliant=0;
		$noncompliant=0; 
		$compliantstaff=array();
		$noncompliantstaff=array();
		$missing_qualifications=0;
		$missing_certificates=0;
		$expired_certificates=0;
		$staff_with_missing_qualifications=0;
		$staff_with_missing_certificates=0;
		$staff_with_expired_certificates=0;
		$staff_with_case=0;
		$staff_with_q_c_case=0;
		$staff_with_q_ec_case=0;
		$staff_with_ec_c_case=0;
		$staff_with_q_c_ec_case=0;
		$each_trade_missing_count=array();
		$staff_missing_qualifications=FALSE;
		$staff_missing_certificates=FALSE;
		$staff_expired_certificates=FALSE;  
		//get the notifications
		$sql_command="SELECT *FROM users_accounts WHERE id = '$userid'";
		$result = mysqli_query($db::$connection,$sql_command) ;
		$result = mysqli_fetch_array($result);
		$notifications=$result['notifications'];
        //get the lengths 
		$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
		$result = mysqli_query($db::$connection,$sql_command) ;
		$result = mysqli_fetch_array($result);
		$lengths=$result['lengths'];
		//get individual column groups
		$lengths_pieces=explode(":",$lengths);
		$mand=explode(",",$lengths_pieces[0]);
		$jobs=explode(",",$lengths_pieces[1]);
		$trade=explode(",",$lengths_pieces[2]);
		 //getting the table trade columns
		$columnnames=array();
		$sqlcolumns="SHOW COLUMNS FROM $tablename";    
		$resultcolumns = mysqli_query($db::$connection,$sqlcolumns) ;
		while ($row= mysqli_fetch_array($resultcolumns))
			{
				$columnnames[]=$row['Field'];
			}
		//conting the rows
		$rCount=0;
		$sqlcommandcount="SELECT *FROM $tablename";
		$resultCount=mysqli_query($db::$connection,$sqlcommandcount) ;
		if(!empty($resultCount))
		{
			if(mysqli_num_rows($resultCount)>0)
			{
				$rCount= mysqli_num_rows($resultCount);            
				//remove the top rows containing trades definitation
			}
		}
		//variable 1: staff count
		$count=$rCount-$trade[1];
		//total lengths
		$length=$mand[1]+$jobs[1]+$trade[1];		
        //get the trades list
		for($c=2+$mand[1]+$jobs[1]; $c<2+$mand[1]+$jobs[1]+$trade[1]; $c++)
		{
			$tradesList[]=$columnnames[$c];
		}
		//initialize variable for missing count for each trade to 0
		for($c=0; $c<count($columnnames); $c++)
		{
			$each_trade_missing_count[]=0;
		}
		//get the compliant count
		for($counting=0; $counting<count($tradesList); $counting++)
		{              
			$value=$tradesList[$counting];
			$index=$counting+1;
			$sqltradesmatrix="SELECT *FROM $tablename WHERE id='$index'";
			$sqlcount="SELECT *FROM $tablename WHERE $value='1'";
			$resultcount=mysqli_query($db::$connection,$sqlcount) ;
			$resulttradesmatrix=mysqli_query($db::$connection,$sqltradesmatrix) ;
			if(empty($resultcount)||empty($resulttradesmatrix))
			{				
				 $response["success"] = 0;
				 $response["message"] = "getting compliant count ". mysqli_error($db::$connection);
				 echo json_encode($response);
			}  
			else
			{
				$rowtrade=mysqli_fetch_array($resulttradesmatrix);
				while ($row= mysqli_fetch_array($resultcount))
				{				
					$broken=FALSE;
					$staff_missing_qualifications=FALSE;
					$staff_missing_certificates=FALSE;
					$staff_expired_certificates=FALSE;  
					if($row['userid']>0 && $row['userid']==$userid)
					{
						//get the staff account
						//$userid=$row['userid'];
						$username="";
						$sqlcommandaccount="SELECT *FROM users_accounts WHERE id = '$userid'";
						$resultaccount = mysqli_query($db::$connection,$sqlcommandaccount) ;
						$rowaccount = mysqli_fetch_array($resultaccount);    
						$username=$rowaccount['usernames'];
						$email=$rowaccount['emails'];
						$position=$rowaccount['position'];
						//the rest of code
						$qualsdata='';
						$certsdata='';
						//get the certificates fot the staff
						$staffaccountid=$row['userid'];
						$sqlcerts="SELECT *FROM $certificatesTable WHERE userid = '$staffaccountid'";
						$resultcerts = mysqli_query($db::$connection,$sqlcerts) ;
						$allcerts=array();
						if (mysqli_num_rows($resultcerts) > 0) 
						{
							//looping through all schemas
							while ($row2= mysqli_fetch_array($resultcerts))
							{
								//temp certificates row
								$certs=array();
								//$certs['id']=$row['id'];
								// $certs['staffaccountid']=$row['staffaccountid'];
								$certs['whereis']=$row2['whereis'];
								//$certs['verified']=$row['verified'];
								$certs['issue']=$row2['issue'];
								$certs['expiry']=$row2['expiry'];
								//  $certs['cert']=$row['cert'];
								//push a single schema into array
								$allcerts[$certs['whereis']]=$certs;
								//array_push($allcerts, $certs); 
							}
						}
						else
						{
							$compCert=-1;
						}
						//get the qualifications for one staff
						for($c=2; $c<2+$mand[1]+$jobs[1]; $c++)
						{  				
							if($rowtrade[$columnnames[$c]]=='1')
							{
								if(!($rowtrade[$columnnames[$c]]==$row[$columnnames[$c]]))
								{
									  //non compliant coz of qualification
									  $missing_qualifications+=1;
									  $staff_missing_qualifications=TRUE;
									  $each_trade_missing_count[$c]+=1;
									  $qualsdata=$qualsdata.'m.'.$columnnames[$c].',';
									  $certsdata=$certsdata.'m.'.$columnnames[$c].',';
									  $broken=TRUE;
									  //break;
								}
								else
								{  
									$qualsdata=$qualsdata.'h.'.$columnnames[$c].',';
									//has the trade , now check certificate validity
									$today= date("Y-m-d");
									$todayD= date("d");  
									$todayM= date("m");  
									$todayY= date("Y");  
									if(array_key_exists($columnnames[$c],$allcerts))
									{
										$localBroken=FALSE;
										$dateIssue=$allcerts[$columnnames[$c]]['issue'];
										$dateExpiry=$allcerts[$columnnames[$c]]['expiry'];
										$dateExpiry= str_replace("E:", "", $dateExpiry);
										$dateExpiry= trim($dateExpiry);
										$datePieces= explode("/", $dateExpiry);
										//create notifications
										$dateexp=date("$datePieces[2]-$datePieces[1]-$datePieces[0]");
										$dateDifference=daysBetween($dateexp,$today);
										createNotificationsWarn($db, $notifications, $dateDifference, $username, $headuser.'_u_notifications', $columnnames[$c], $dateExpiry, $userid);
										if((int)$todayY>(int)$datePieces[2])
										{
											//the certificate has expired
											createNotifications($db, $username, $headuser.'_u_notifications', $columnnames[$c], $dateExpiry, $userid);
											$expired_certificates+=1;
											$staff_expired_certificates=TRUE;
											$broken=TRUE;
											$localBroken=TRUE;
										}
										else if((int)$todayY==(int)$datePieces[2])
										{
											if((int)$todayM>(int)$datePieces[1])
											{
												 //the certificate has expired
												 createNotifications($db, $username, $headuser.'_u_notifications', $columnnames[$c], $dateExpiry, $userid);
												 $expired_certificates+=1;
												 $staff_expired_certificates=TRUE;
												 $broken=TRUE;
												 $localBroken=TRUE;
											}
											else if((int)$todayM==(int)$datePieces[1])
											{
												if((int)$todayD>(int)$datePieces[0])
												{
													//the certificate has expired
													createNotifications($db, $username, $headuser.'_u_notifications', $columnnames[$c], $dateExpiry, $userid);
													$expired_certificates+=1;
													$staff_expired_certificates=TRUE;
													$broken=TRUE;
													$localBroken=TRUE;
												}
												else
												{
													//check when the certificate will expire
												}
											}

										 }
										 if($localBroken)
											 $certsdata=$certsdata.'e.'.$columnnames[$c].'.'.$dateIssue.'.'.$dateExpiry.',';
										 else
											 $certsdata=$certsdata.'h.'.$columnnames[$c].'.'.$dateIssue.'.'.$dateExpiry.',';
									 }
									else
									{
										$certsdata=$certsdata.'m.'.$columnnames[$c].',';
										$missing_certificates+=1;
										$staff_missing_certificates=TRUE;
										$broken=TRUE;
									}
					  
								}
							}                
				
						}
					}
					else
					   continue;
					if($broken==FALSE)
					{
						$compliant+=1;
						$userinfo=$row['id'].':'.$userid.':'.$tradesList[$counting].':'.$username.':'.$email.':'.$position.':'.$qualsdata.':'.$certsdata;
						array_push($compliantstaff, $userinfo);
					}
					else
					{
					   $noncompliant+=1;
						$userinfo=$row['id'].':'.$userid.':'.$tradesList[$counting].':'.$username.':'.$email.':'.$position.':'.$qualsdata.':'.$certsdata;
						array_push($noncompliantstaff, $userinfo);
					}
					if($staff_missing_qualifications==TRUE)
					   $staff_with_missing_qualifications+=1;
					if($staff_missing_certificates==TRUE)
					   $staff_with_missing_certificates+=1;
					if($staff_expired_certificates==TRUE)
					   $staff_with_expired_certificates+=1;
					if($staff_missing_qualifications==TRUE && $staff_missing_certificates==TRUE)
					   $staff_with_q_c_case+=1;
					else if($staff_missing_qualifications==TRUE && $staff_expired_certificates==TRUE)
					   $staff_with_q_ec_case+=1;
					else if($staff_missing_certificates==TRUE && $staff_expired_certificates==TRUE)
					   $staff_with_ec_c_case+=1;
					else if($staff_missing_qualifications==TRUE && $staff_missing_certificates==TRUE && $staff_expired_certificates==TRUE)
					   $staff_with_q_c_ec_case+=1;
					else if($staff_missing_qualifications==TRUE || $staff_missing_certificates==TRUE || $staff_expired_certificates==TRUE)
					   $staff_with_case+=1;
				   
			   
				}
			
			}
		}
		 $response["success"] = 1; 
		 $response["staffcount"]=$count;
		 $response["compliant"]=$compliant;
		 $response["noncompliant"]=$noncompliant;
		 $response["compliantstaff"]=array();
		 $response["compliantstaff"]=$compliantstaff;
		 $response["noncompliantstaff"]=array();
		 $response["noncompliantstaff"]=$noncompliantstaff;
		 $response["missingqualifications"]=$missing_qualifications;
		 $response["missingcertificates"]=$missing_certificates;
		 $response["expiredcertificates"]=$expired_certificates;
		 $response["staffwithmissingqualifications"]=$staff_with_missing_qualifications;
		 $response["staffwithmissingcertificates"]=$staff_with_missing_certificates;
		 $response["staffwithexpiredcertificates"]=$staff_with_expired_certificates;     
		 $response["staffwith_case"]=$staff_with_case;
		 $response["staffwith_q_c_case"]=$staff_with_q_c_case;
		 $response["staffwith_q_e_case"]=$staff_with_q_ec_case;
		 $response["staffwith_c_ec_case"]=$staff_with_ec_c_case;
		 $response["staffwith_q_c_ec_case"]=$staff_with_q_c_ec_case;
		 $response["each_qual_missing_count"]=array();
		 $response["each_qual_missing_count"]=$each_trade_missing_count;
		 $response["columnnames"]=array();
		 $response["columnnames"]=$columnnames; 
        echo json_encode($response);
       
   
	}
	else 
	{
		// required field is missing
		$response["success"] = 0;
		$response["message"] = "Required field(s) is missing";
		// echoing JSON response
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
function createNotifications($db, $username, $table, $columnName, $dateExpiry, $userid)
{
	//get current date
	$today= date("d-m-Y H:i");
	$message="Certificate for ".$columnName." expired on ".$dateExpiry;
	$sql_command_check="SELECT *FROM $table WHERE  messages = '$message'";
	$result_check = mysqli_query($db::$connection,$sql_command_check) ;
	if(!(!$result_check || mysqli_num_rows($result_check)>0))
	{
		//the notification is not there
        $sql_command_insert="INSERT INTO $table (classes,messages,dateadded)"
			. " VALUES('1', '$message', '$today')";
		$result = mysqli_query($db::$connection,$sql_command_insert) ;
		if(!$result)
		{
			$response["success"] = 0;
			$response["message"] = "adding expired notification 0". mysqli_error($db::$connection);
			echo json_encode($response);
		}
	}
}
function createNotificationsWarn($db, $notifications, $dateDifference, $username, $table, $columnName, $dateExpiry, $userid)
{
	//get current date
	$today= date("d-m-Y H:i");
	$changed=FALSE;
	$noti_pieces=explode(":", $notifications);
	$message="";
	for($c=0; $c<count($noti_pieces); $c++)
	{
		//one week
      if($c=='0' && $dateDifference>=7  && $dateDifference<9)
         {
             $message=" Certificate for ".$columnName." will expire in 1 week, ".$dateExpiry;
			 $changed=TRUE;
          }
      //two weeks
      if($c=='1' && $dateDifference>=14  && $dateDifference<16)
         {
             $message="Certificate for ".$columnName." will expire in 2 weeks, ".$dateExpiry;
			 $changed=TRUE;
         }
      //one month
      if($c=='2' && $dateDifference>=30  && $dateDifference<32 )
        {
             $message="Certificate for ".$columnName." will expire in 1 month, ".$dateExpiry;
			 $changed=TRUE;
        }
      //three months
      if($c=='3' && $dateDifference>=90 && $dateDifference<92)
        {
             $message="Certificate for ".$columnName." will expire in 3 months, ".$dateExpiry;
			 $changed=TRUE;
        }
      //6 months
      if($c=='4' && $dateDifference>=180 && $dateDifference<182)
       {
             $message="Certificate for ".$columnName." will expire in 6 months, ".$dateExpiry;
			 $changed=TRUE;
       }
      //one year
      if($c=='5' && $dateDifference>=365 && $dateDifference<367)
       {
             $message="Certificate for ".$columnName." will expire in 1 year, ".$dateExpiry;
			 $changed=TRUE;
       }
	}
	if($changed)
	{
		$sql_command_check="SELECT *FROM $table WHERE messages = '$message'";
		$result_check = mysqli_query($db::$connection,$sql_command_check) ;
		if(!(!$result_check || mysqli_num_rows($result_check)>0))
		{
			//the notification is not there
			$sql_command_insert="INSERT INTO $table (classes,messages,dateadded)"
				. " VALUES('1', '$message', '$today')";
			$result = mysqli_query($db::$connection,$sql_command_insert) ;
			if(!$result)
			{
				$response["success"] = 0;
				$response["message"] = "adding expired notification ". mysqli_error($db::$connection);
				echo json_encode($response);
			}
		}
		
	}
	  
}
function daysBetween($dt1,$dt2)
{
    return date_diff(date_create($dt2), date_create($dt1))->format('%r%a');
}
?>