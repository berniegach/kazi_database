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
if (isset($_GET['tablename'])&&isset($_GET['tradename']))
    {
    $tablename=$_GET['tablename']; 
    $tradesList=array();
    $tradename=$_GET['tradename'];
    $tCount;
    $allcerts=array();
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
    
    //get the trades count
    $sqltrades="SELECT *FROM schemas_table WHERE name = '$tablename'";   
    $resulttrades = mysqli_query($db::$connection,$sqltrades) ;
    if(empty($resulttrades))
    {
        $response["success"] = 0;
        $response["message"] = "enpty no trades". mysqli_error($db::$connection);
        echo json_encode($response);

    }    
    if (mysqli_num_rows($resulttrades) > 0) 
    {
         $resulttrades = mysqli_fetch_array($resulttrades);             
         $schemas=array();            
         $tCount=$resulttrades['tlength'];
         //get total rows in the table
         $rCount=0;
         $sqlcommandcount="SELECT *FROM $tablename WHERE $tradename='1'";
         $resultCount=mysqli_query($db::$connection,$sqlcommandcount) ;
         if(!empty($resultCount))
         {
            if(mysqli_num_rows($resultCount)>0)
         {
         $rCount= mysqli_num_rows($resultCount);            
         //remove the top rows containing trades definitation
         }
         }
         $count=$rCount-1;
    }
    else
        {
         $response["success"] = 0;
         $response["message"] = "no trades". mysqli_error($db::$connection);
         echo json_encode($response);
    }
     //getting the table trade columns
    $columnnames=array();
    $sqlcolumns="SHOW COLUMNS FROM $tablename";    
    $resultcolumns = mysqli_query($db::$connection,$sqlcolumns) ;
    if(empty($resultcolumns))
    {
        $response["success"] = 0;
        $response["message"] = "getting columns names ". mysqli_error($db::$connection);
        echo json_encode($response);
    }    
    else
    {
        $numColumns= mysqli_num_rows($resultcolumns);
        while ($row= mysqli_fetch_array($resultcolumns))
        {
        $columnnames[]=$row['Field'];
        } 
    }

    
     for($c=count($columnnames)-$tCount; $c<count($columnnames); $c++)
    {
        $tradesList[]=$columnnames[$c];
    }
     //get the fields array
    for($c=0; $c<count($columnnames); $c++)
    {
        $each_trade_missing_count[]=0;
    }
     $index;
    for($c=0; $c<count($tradesList); $c++)
    {
        if($tradesList[$c]==$tradename)
        {
            $index=$c+1;
        }
    }
 
    $compliant=0;
    $noncompliant=0; 
    //get the compliant count
               
        $sqltradesmatrix="SELECT *FROM $tablename WHERE id='$index'";
        $sqlcount="SELECT *FROM $tablename WHERE $tradename='1'";
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
                //get the certificates fot the staff
                $staffaccountid=$row['userid'];
                $sqlcerts="SELECT *FROM certificates WHERE staffaccountid = '$staffaccountid'";
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
                             // $certs['issue']=$row['issue'];
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
                        //get the qualifications
                 for($c=2; $c<(sizeof($columnnames)- $tCount); $c++)
                   {
                     
                      
                     
                        if($rowtrade[$columnnames[$c]]=='1')
                     {
                          if(!($rowtrade[$columnnames[$c]]==$row[$columnnames[$c]]))
                        {
                              //non compliant coz of trade
                              
                              $missing_qualifications+=1;
                              $staff_missing_qualifications=TRUE;
                              $each_trade_missing_count[$c]+=1;
                              $broken=TRUE;
                              //break;
                        }
                        else
                        {
                            
                            //has the trade , now check certificate validity
                            $todayD= date("d");  
                            $todayM= date("m");  
                            $todayY= date("Y");  
                            if(strlen($allcerts[(string)$c]['expiry'])>0)
                            {
                                $dateExpiry=$allcerts[(string)$c]['expiry'];
                                $dateExpiry= str_replace("E:", "", $dateExpiry);
                                $dateExpiry= trim($dateExpiry);
                                $datePieces= explode("/", $dateExpiry);
                                if((int)$todayY>(int)$datePieces[2])
                                {
                                    $expired_certificates+=1;
                                    $staff_expired_certificates=TRUE;
                                    $broken=TRUE;
                                }
                                else if((int)$todayY==(int)$datePieces[2])
                                {
                                    if((int)$todayM>(int)$datePieces[1])
                                    {
                                          $expired_certificates+=1;
                                          $staff_expired_certificates=TRUE;
                                          $broken=TRUE;
                                    }
                                    else if((int)$todayM==(int)$datePieces[1])
                                    {
                                        if((int)$todayD>(int)$datePieces[0])
                                        {
                                            $expired_certificates+=1;
                                            $staff_expired_certificates=TRUE;
                                            $broken=TRUE;
                                        }
                                    }

                                }
                            }
                            else
                            {
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
                    $compliant+=1;
               else 
                   $noncompliant+=1;
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

     $response["success"] = 1; 
     $response["staffcount"]=$count;
     $response["compliant"]=$compliant;
     $response["noncompliant"]=$noncompliant;
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

    //$response["comps"]=array();
    // array_push($response["comps"], $comps);
   
    echo json_encode($response);
       
   
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
 
    // echoing JSON response
     echo json_encode($response);
}
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