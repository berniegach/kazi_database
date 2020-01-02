<?php
 
/*
 * get staff account id in the account table from the schema table
 */
 
// array for JSON response
$response = array();
// include db connect class
require_once __DIR__ . '/db_connect.php';
 
// connecting to db
$db = new DB_CONNECT();
 
// check for required fields
if (isset($_POST['id']))
    {
		$id = $_POST['id']; 
		//get the table name
		$head=makeTableName((string)$id);
		$tablename=$head.'_equipments_matrix';
		$certificatesTable=$head.'_certificates_equipments';
		//other variables
		$staffCount=0;
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
        //get the lengths
		$sql_command="SELECT *FROM contractors_accounts WHERE id = '$id'";
		$result = mysqli_query($db::$connection,$sql_command) ;
		$result = mysqli_fetch_array($result);
		$lengths=$result['lengthsequipments'];
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
		//get the unique column counts
		$sql_command_users="SELECT DISTINCT userid FROM $tablename";
		$result_users=mysqli_query($db::$connection,$sql_command_users) ;
		if(!empty($result_users))
		{
			$staffCount=mysqli_num_rows($result_users)-1;
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
					if($row['userid']>0)
					{
						$qualsdata='';
						$certsdata='';
						//get the certificates fot the staff
						$staffaccountid=$row['userid'];
						$sqlcerts="SELECT *FROM $certificatesTable WHERE userid = '$staffaccountid'";
						$resultcerts = mysqli_query($db::$connection,$sqlcerts) ;
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
						//variable to make sure we dont keep checking the same certificate over and over
						$certChecked=FALSE;
						//get the qualifications for one staff
						for($c=2; $c<2+$mand[1]+$jobs[1]; $c++)
						{  				
							if($rowtrade[$columnnames[$c]]=='1')
							{
								//check if the person has the required qualification
								if(!($rowtrade[$columnnames[$c]]==$row[$columnnames[$c]]))
								{
									  //non compliant coz of trade
									  $missing_qualifications+=1;
									  $staff_missing_qualifications=TRUE;
									  $each_trade_missing_count[$c]+=1;
									  $qualsdata=$qualsdata.'m.'.$columnnames[$c].',';
									  //$certsdata=$certsdata.'m.'.$columnnames[$c].',';
									  $broken=TRUE;
									  //break;
								}
								else
								{ 
									$qualsdata=$qualsdata.'h.'.$columnnames[$c].',';							
									//has the trade , now check certificate validity
									$todayD= date("d");  
									$todayM= date("m");  
									$todayY= date("Y"); 
									//check if the certificate for the equipment is there 
									//unlike for the skills where we compare the certificate name to the qualification here we use the equipment name
									if(!$certChecked && array_key_exists($value,$allcerts))
									{
										$certChecked=TRUE;
										$localBroken=FALSE;
										$dateIssue=$allcerts[$value]['issue'];
										$dateExpiry=$allcerts[$value]['expiry'];
										$dateExpiry= str_replace("E:", "", $dateExpiry);
										$dateExpiry= trim($dateExpiry);
										$datePieces= explode("/", $dateExpiry);
										if((int)$todayY>(int)$datePieces[2])
										{
											$expired_certificates+=1;
											$staff_expired_certificates=TRUE;
											$broken=TRUE;
											$localBroken=TRUE;
										}
										else if((int)$todayY==(int)$datePieces[2])
										{
											if((int)$todayM>(int)$datePieces[1])
											{
												 $expired_certificates+=1;
												 $staff_expired_certificates=TRUE;
												 $broken=TRUE;
												 $localBroken=TRUE;
											}
											else if((int)$todayM==(int)$datePieces[1])
											{
												if((int)$todayD>(int)$datePieces[0])
												{
													$expired_certificates+=1;
													$staff_expired_certificates=TRUE;
													$broken=TRUE;
													$localBroken=TRUE;
												}
											}

										 }
										 if($localBroken)
											 $certsdata=$certsdata.'e.'.$value.'.'.$dateIssue.'.'.$dateExpiry.',';
										 else
											 $certsdata=$certsdata.'h.'.$value.'.'.$dateIssue.'.'.$dateExpiry.',';
									 }
									else if(!$certChecked)
									{
										$certChecked=TRUE;
										$certsdata=$certsdata.'m.'.$value.',';
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
				   //save the user profile as non compliant
					if($broken==FALSE)
					{
						$compliant+=1;
						// get an account from staff accounts table
						$userid=$row['userid'];
						$username="";
						$sqlcommandaccount="SELECT *FROM users_accounts WHERE id = '$userid'";
						$resultaccount = mysqli_query($db::$connection,$sqlcommandaccount) ;
						if(!empty($resultaccount))
							{
							$rowaccount = mysqli_fetch_array($resultaccount);    
							$username=$rowaccount['usernames'];
							$username=$rowaccount['usernames'];
							$email=$rowaccount['emails'];
							$position=$rowaccount['position'];
							}
						$userinfo=$row['id'].':'.$userid.':'.$tradesList[$counting].':'.$username.':'.$email.':'.$position.':'.$qualsdata.':'.$certsdata;
						array_push($compliantstaff, $userinfo);
					}
					else
					{
						//save the user profile as compliant
					   $noncompliant+=1;
						$username="";
						$userid=$row['userid'];
						$sqlcommandaccount="SELECT *FROM users_accounts WHERE id = '$userid'";
						$resultaccount = mysqli_query($db::$connection,$sqlcommandaccount) ;
						if(!empty($resultaccount))
						  {
								$rowaccount = mysqli_fetch_array($resultaccount);    
								$username=$rowaccount['usernames'];
								$username=$rowaccount['usernames'];
								$email=$rowaccount['emails'];
								$position=$rowaccount['position'];
						  }
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
		 $response["staffcount"]=$staffCount;
		 $response["equipscount"]=$count;
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
 
?>