<?php

namespace App\Controllers;

class CustomerDetails extends Controller
{
    public function getUnpaidCustomers($request, $response)
    {
        $this->db->beginTransaction();

        $sql = 'Select Customer_Name, Wallet from customer_details where Wallet <0 order by Customer_Name ';

        $handle = $this->db->prepare($sql);
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Customer_Grid'] = $data;
        
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
    //    return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
    }

    public function authentication($request, $response)
    {
        $args = $request->getParsedBody();
        
        $this->db->beginTransaction();
        
        $handle = $this->db->prepare('Select Customer_Mobno, Customer_AddressDetails, Address_Line1, Address_Line2, Area, City, State, Pincode, Customer_Emailid,Customer_Name,Customer_Password, Wallet,isAdmin,Account_Status,Applicable_Promocodes, Referral_Code, isRefProcessed from customer_details where Customer_Mobno = ?');
           
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Customer_Password']=$args['password'];
        $_REQUEST['Category']=ucfirst($args['category']);

        $handle->bindValue(1, $_REQUEST['Customer_Mobno']);
        $result = $handle->execute();
    
        $data = $handle->fetchAll();
        $arr = array_values($data);         

        $errresult['Resultcode'] = static::$messages['Resultcode_0'];

        if($data) {

            $errresult['Message'] = static::$messages['Data_true'];
            $Password_Authentication=password_verify($_REQUEST['Customer_Password'],$arr[0]['Customer_Password']);

            $Admin_Authentication=false;
            if($_REQUEST['Category']==static::$messages['Admin']) {
                if($arr[0]['isAdmin']==1)
                    $Admin_Authentication=true;
            }

            else if($_REQUEST['Category']==static::$messages['Customer']) {
                if($arr[0]['isAdmin']==0)
                    $Admin_Authentication=true;
            }
            
            
            if($Admin_Authentication)
            {
                $status_authentication=false;
                if($arr[0]['Account_Status']==static::$messages['Active'])
                $status_authentication=true;

                if($status_authentication)
                {
                    if($Password_Authentication) {


                        $applicable_promocodes=null;

                        if($arr[0]['Applicable_Promocodes']!=null){
                            $promoNames=preg_split("/[\s]+/",trim($arr[0]['Applicable_Promocodes']));
                            
                            foreach( $promoNames as $name ) {
                     
                                $handle1 = $this->db->prepare('Select Name, Message from promocodes where Name in (?)');
                                $handle1->bindValue(1, $name);

                                $result1 = $handle1->execute();
                                $data1 = $handle1->fetchAll();
                                $arr1 = array_values($data1);
                                $applicable_promocodes[$name]=$arr1[0]['Message'];
                            }
                        }
           
                        $auth = array
                            (
                                "Customer_Mobno"=>$arr[0]['Customer_Mobno'],
                                "EmailId"=>$arr[0]['Customer_Emailid'],
                                "Customer_Name"=>$arr[0]['Customer_Name'],
                                "Customer_AddressDetails"=>$arr[0]['Customer_AddressDetails'],
                                "Address_Line1"=>$arr[0]['Address_Line1'],
                                "Address_Line2"=>$arr[0]['Address_Line2'],
                                "Area"=>$arr[0]['Area'],
                                "State"=>$arr[0]['State'],
                                "City"=>$arr[0]['City'],
                                "Pincode"=>$arr[0]['Pincode'],
                                "Wallet"=>$arr[0]['Wallet'],
                                "Account_Status"=>$arr[0]['Account_Status'],
                                "Applicable_Promocodes"=>$applicable_promocodes,
                                "Referral_Code"=>$arr[0]['Referral_Code'],
                                "isRefProcessed"=>$arr[0]['isRefProcessed'],
                                "Authenticated"=>$Password_Authentication
                            );
                 
                        $errresult['Data']=$auth;
                    }
                    else {
                            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
                            $errresult['Message'] = static::$messages['Check_Password'];
                            $errresult['Data'] = static::$messages['No_Data'];
                        }
                     
                }
                else {
                    $errresult['Resultcode'] = static::$messages['Resultcode_1'];
                    $errresult['Message'] = static::$messages['Account_Status_Not_Active'];
                    $errresult['Data'] = static::$messages['No_Data'];
                }
                     
            }

            else {
                $errresult['Resultcode'] = static::$messages['Resultcode_1'];
                $errresult['Message'] = static::$messages['Check_Category'];
                $errresult['Data'] = static::$messages['No_Data'];
         //       $errresult['Data1'] = array(array());

            }
            
         }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];            
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
        return $response//->withJson($errresult,JSON_PRETTY_PRINT);
        ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    }

    public function getReferralCode($key) 
    { 
        $s = strtoupper(md5(uniqid($key,true))); 
        $guidText = substr($s,0,8); 
        return $guidText;
    }

    public function createNewCustomer($request, $response)
    {
       // $this->sms();
        $args = $request->getParsedBody();
        
        $this->db->beginTransaction();

        $handle = $this->db->prepare('insert into customer_details(Customer_Name,Customer_Mobno,Customer_Emailid,Customer_AddressDetails,Address_line1,Address_line2, Area, City, State, Pincode,Customer_GPSLan,Customer_GPSLon,Customer_Password,isAdmin,Account_Status,Wallet,Referree_Code,Referral_Code,isRefProcessed) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    
        $_REQUEST['Category']=ucfirst($args['category']);
        $_REQUEST['Customer_Name']=ucwords($args['name']);
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Customer_Emailid']=$args['emailid'];
        $_REQUEST['Customer_AddressDetails']=$args['address'];
        $_REQUEST['Address_line1']=$args['address_line1'];
        $_REQUEST['Address_line2']=$args['address_line2'];
        $_REQUEST['Area']=$args['area'];
        $_REQUEST['City']=$args['city'];
        $_REQUEST['State']=$args['state'];
        $_REQUEST['Pincode']=$args['pincode'];

        $_REQUEST['Customer_GPSLan']=$args['lat'];
        $_REQUEST['Customer_GPSLon']=$args['lon'];
        $_REQUEST['Customer_Password']=$args['password'];
        $_REQUEST['Referree_Code']=$args['refcode'];
        $_REQUEST['isRefProcessed']=$args['isrefprocessed'];


        $handle->bindValue(1, $_REQUEST['Customer_Name']);
        $handle->bindValue(2, $_REQUEST['Customer_Mobno']);
        $handle->bindValue(3, $_REQUEST['Customer_Emailid']);
        $handle->bindValue(4, $_REQUEST['Customer_AddressDetails']);
        $handle->bindValue(5, $_REQUEST['Address_line1']);
        $handle->bindValue(6, $_REQUEST['Address_line2']);
        $handle->bindValue(7, $_REQUEST['Area']);
        $handle->bindValue(8, $_REQUEST['City']);
        $handle->bindValue(9, $_REQUEST['State']);
        $handle->bindValue(10, $_REQUEST['Pincode']);



        $handle->bindValue(11, $_REQUEST['Customer_GPSLan']);
        $handle->bindValue(12, $_REQUEST['Customer_GPSLon']);
        $handle->bindValue(13, password_hash($_REQUEST['Customer_Password'], PASSWORD_DEFAULT));

        if($_REQUEST['Category']==static::$messages['Admin']) {
            $_REQUEST['Account_Status']=static::$messages['Active'];
            $handle->bindValue(14, 1);
            $handle->bindValue(17, 'NOTAPPLY');
            $handle->bindValue(19, 'U');
        }
        else if($_REQUEST['Category']==static::$messages['Customer']) {
            $_REQUEST['Account_Status']=static::$messages['Waiting_For_Verification'];    
            $handle->bindValue(14, 0);
            $handle->bindValue(17, $_REQUEST['Referree_Code']);
            $handle->bindValue(19, $_REQUEST['isRefProcessed']);
        }

        $handle->bindValue(15, $_REQUEST['Account_Status']);
        $handle->bindValue(16, 0);
        $handle->bindValue(18, $this->getReferralCode($_REQUEST['Customer_Mobno']));


        $result = $handle->execute();
        $id = $this->db->lastInsertId();

        $today = date("Y-m-d");
        
        $handle2 = $this->db->prepare('select Name from promocodes where Criteria=? and Valid=? and Valid_From<= ? and Valid_To >=?');
                   
        $handle2->bindValue(1, "Order_0");
        $handle2->bindValue(2, 'Y');
        $handle2->bindValue(3, $today);
        $handle2->bindValue(4, $today);
 
        $result2 = $handle2->execute();
        $data2 = $handle2->fetchAll();
         
        if($data2!=null) {
            $arr = array_values($data2);
            $name=$arr[0]['Name'];
            $handle2 = $this->db->prepare('update customer_details set Applicable_Promocodes= ? where id= ?');
                           
            $handle2->bindValue(1, $name);
            $handle2->bindValue(2, $id);
            $result2 = $handle2->execute();
        }  
    
        $handle3 = $this->db->prepare('select * from customer_details where id= ?');
        $handle3->bindValue(1, $id);
        $result3 = $handle3->execute();
        $data= $handle3->fetchObject();
//        $sms_arr = array_values($data);
//$errresult['Resultco'] =$data->Id;
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;
        $errresult['Message'] = static::$messages['Data_true'].' '.static::$messages['Customer_Created'];
        $errresult['Data'] = $data;
        $errresult['StatusCode'] = $handle->errorCode();
        
      
      
        //return $response->withJson($errresult);
        $sms_number=$data->Customer_Mobno;
        if($data->Referree_Code != null)
            $sms_type="create_new_customer_with_referralcode"; 
        else
            $sms_type="create_new_customer";
        $sms_data=array($data->Customer_Name,$data->Referree_Code,null);
        $this->testsms($sms_number,$sms_type,$sms_data);
      
        $this->db->commit();
        $this->db = null;
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
    }
 
    public function getCustomerHistory($request, $response)
    {

        $handle = $this->db->prepare('select temp.Customer_Name,
            temp. Customer_Mobno,temp.Account_Status,temp.Total_Orders, 
            IFNULL(temp.Total_Amount,0) AS Total_Amount,temp.Wallet
            from (select c.Customer_Name,c.Customer_Mobno, 
            c.Account_Status ,count(o.Order_Id) as Total_Orders,sum(o.Cost)
            as Total_Amount,c.Wallet
            from customer_details c
            left join order_details o on c.Customer_Mobno=o.Customer_Mobno
            group by c.Customer_Mobno,c.Account_Status) temp order by 
            temp.Customer_Name,temp.Total_Orders desc' );
       
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Customer_Grid'] = $data;
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
    }

    public function getUser($request, $response)
    {
       // $args = $request->getQueryParams();
         $args = $request->getParsedBody();
        $handle = $this->db->prepare('Select * from customer_details where  
            Customer_Mobno=:Customer_Mobno LIMIT 1');
          
        $handle->bindParam('Customer_Mobno', $args['mobno']);

        $result = $handle->execute();
        $data = $handle->fetchObject();

        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {

            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $data;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
        return $response->withJson($errresult);
    }

    public function getVerificationAccounts($request, $response)
    {  
        $handle = $this->db->prepare('Select * from customer_details where
            Account_Status in (:status1,:status2) order by Account_Status' );
   
        $status1 = static::$messages['Waiting_For_Verification'];
        $status2 = static::$messages['ReVerification'];

  
        $handle->bindParam('status1', $status1);
        $handle->bindParam('status2', $status2);

        $result = $handle->execute();
    
        $data = $handle->fetchAll();
        $dataSend['Customer_Grid'] = $data;
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    }

    public function setAccountStatus($request, $response)
    {
        $args = $request->getParsedBody();
        
        $this->db->beginTransaction();

        $handle1 = $this->db->prepare('Select * from customer_details where  
          Customer_Mobno= :Customer_Mobno');
        $handle1->bindParam('Customer_Mobno', $args['mobno']);
        $result1= $handle1->execute();
          
        $data = $handle1->fetchAll();
        $arr = array_values($data);
    
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        $sms_type=null;
        if($data) {

            $handle = $this->db->prepare('update customer_details set   
                Account_Status = :status, Wallet =(Wallet + :Wallet) where 
                Customer_Mobno = :Customer_Mobno ');
   
            $handle->bindParam('status', $args['status']);
            $handle->bindParam('Customer_Mobno', $args['mobno']);
            $handle->bindParam('Wallet', $args['wallet']);

            $errresult['Message'] = static::$messages['Data_true'];

            if($args['status']!=$arr[0]['Account_Status']) {
                
                $errresult['Message'] = $errresult['Message'].' '.static::$messages['Account_Status_Updated'];
                $sms_type="change_account_status";    
                $sms_data=array($args['status'],null,null);

            }

            if($args['wallet'] > 0) {
                $handle->bindParam('Wallet', $args['wallet']);
                $errresult['Message'] = $errresult['Message'].' '.static::$messages['Wallet_Updated'];
                $sms_type="wallet_updated_by_customer"; 
                $sms_data=array($args['wallet'],null,null);                   
            }
            else {
                $amount=0;
                $handle->bindParam('Wallet', $amount);
            }

            $result = $handle->execute();
            $result1= $handle1->execute();
        
            $data = $handle1->fetchObject();
       
            $errresult['Data'] = $data;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle1->errorCode();

        $sms_number=$data->Customer_Mobno;
        
        if($sms_type!=null)$this->testsms($sms_number,$sms_type,$sms_data);
             
        $this->db->commit();
        $this->db = null;
      
        return $response->withJson($errresult);
    }  

    public function setPassword($request, $response)
    {
        $args = $request->getParsedBody();
        
        $this->db->beginTransaction();

        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Customer_Password']=$args['password'];

        $handle1 = $this->db->prepare('Select * from customer_details where  
            Customer_Mobno= :Customer_Mobno');
        $handle1->bindParam('Customer_Mobno', $args['mobno']);
        $result1= $handle1->execute();
          
        $data = $handle1->fetchAll();
        $arr = array_values($data);
 
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {

            $handle = $this->db->prepare('update customer_details set Customer_Password = ? where Customer_Mobno = ?');
    
            $handle->bindValue(1, password_hash($_REQUEST['Customer_Password'], PASSWORD_DEFAULT));
    
            $handle->bindValue(2, $_REQUEST['Customer_Mobno']);
            $result = $handle->execute();

            $result1= $handle1->execute();
            $data = $handle1->fetchObject();
            
            $errresult['Message'] = static::$messages['Data_true'].' '.static::$messages['Password_Updated'];
            $errresult['Data'] = $data;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle1->errorCode();
        $this->db->commit();
        $this->db = null;
      
        return $response->withJson($errresult);
    }  

    public function updateCustomer($request, $response)
    {
        $args = $request->getParsedBody();
        
        $this->db->beginTransaction(); 
  
        $_REQUEST['Category']=$args['category'];
        $_REQUEST['Customer_Name']=$args['name'];
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['New_Mobno']=$args['newmobno'];
        $_REQUEST['Customer_Emailid']=$args['emailid'];
        $_REQUEST['Customer_AddressDetails']=$args['address'];

        $_REQUEST['Address_Line1']=$args['address_line1'];
        $_REQUEST['Address_Line2']=$args['address_line2'];
        $_REQUEST['Area']=$args['area'];
        $_REQUEST['City']=$args['city'];
        $_REQUEST['State']=$args['state'];
        $_REQUEST['Pincode']=$args['pincode'];

        $_REQUEST['Customer_GPSLan']=$args['lat'];
        $_REQUEST['Customer_GPSLon']=$args['lon'];
        $_REQUEST['Customer_Password']=$args['oldpassword'];
        $_REQUEST['New_Password']=$args['newpassword'];
        $_REQUEST['Wallet']=$args['wallet'];

        $handle1 = $this->db->prepare('Select * from customer_details where Customer_Mobno=?');
        $handle1->bindValue(1, $_REQUEST['Customer_Mobno']);
        $result1= $handle1->execute();
    
        $data = $handle1->fetchAll();
   
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {

            $arr = array_values($data);
            $id=$arr[0]['Id'];   

            $Password_Authentication=password_verify($_REQUEST['Customer_Password'],$arr[0]['Customer_Password']);

            if($Password_Authentication) {
                
                $errresult['Message'] = static::$messages['Data_true'];
                
                $handle = $this->db->prepare('update customer_details set Customer_Name = ?,Customer_Emailid = ?,Customer_AddressDetails = ?, Address_line1 = ?, Address_line2 = ?, Area = ?, City = ?, State = ?, Pincode = ?,Customer_GPSLan = ?,Customer_GPSLon = ?,isAdmin= ?,Account_Status = ? ,Customer_Password=?, Wallet=(Wallet+?), Customer_Mobno=? where Id = ?');
      
                $handle->bindValue(1, $_REQUEST['Customer_Name']);
                $handle->bindValue(2, $_REQUEST['Customer_Emailid']);
                $handle->bindValue(3, $_REQUEST['Customer_AddressDetails']);
            //    $handle->bindValue(4, $_REQUEST['Address_line1']);
            //    $handle->bindValue(5, $_REQUEST['Address_line2']);
           //     $handle->bindValue(6, $_REQUEST['Area']);
            //    $handle->bindValue(7, $_REQUEST['City']);
            //    $handle->bindValue(8, $_REQUEST['State']);
            //    $handle->bindValue(9, $_REQUEST['Pincode']);


                $handle->bindValue(10, $_REQUEST['Customer_GPSLan']);
                $handle->bindValue(11, $_REQUEST['Customer_GPSLon']);

                $_REQUEST['Account_Status']=$arr[0]['Account_Status'];
                if($_REQUEST['Category']=="admin")                  
                    $handle->bindValue(12, 1);
                
                else if($_REQUEST['Category']=='customer') 
                    $handle->bindValue(12, 0);
                
                $address = array(
                    "Address_Line1" => 4,
                    "Address_Line2" => 5,
                    "Area" => 6,
                    "City" => 7,
                    "State" => 8,
                    "Pincode" => 9
                );

                $isAddressUpdated=0;
                foreach ($address as $key => $value) {
                    
                    if($_REQUEST[$key]==null||$_REQUEST[$key]==$arr[0][$key]) 
                       $handle->bindValue($value, $arr[0][$key]);
                        
                    else {
                        $handle->bindValue($value, $_REQUEST[$key]);
                        $isAddressUpdated=1;
                        $_REQUEST['Account_Status']=static::$messages['ReVerification'];
                    }

                }

                if($isAddressUpdated==1)
                    $errresult['Message'] = $errresult['Message'].' '.static::$messages['Address_Updated'].' '.static::$messages['Admin_ReVerify'];
                   
                $handle->bindValue(17, $id);

                //updating Password      
                if($_REQUEST['New_Password']==null||$_REQUEST['New_Password']==$_REQUEST['Customer_Password']) {
                
                       $handle->bindValue(14, password_hash($_REQUEST['Customer_Password'], PASSWORD_DEFAULT));
                }
                else {
                    $handle->bindValue(14, password_hash($_REQUEST['New_Password'], PASSWORD_DEFAULT));

                    $errresult['Message'] = $errresult['Message'].' '.static::$messages['Password_Updated'];
                }

                //updating wallet
                if($_REQUEST['Wallet']>0) {
                    $handle->bindValue(15, $_REQUEST['Wallet']);

                    $errresult['Message'] = $errresult['Message'].' '.static::$messages['Wallet_Updated'];

                    $handle4 = $this->db->prepare("insert into transactions (Customer_Mobno,Amount,Comment) values (?,?,?)");

                    $comment=static::$messages['Amount_Rs'].$_REQUEST['Wallet'].' '.static::$messages['Amount_Credit'];
                    $handle4->bindValue(1, $_REQUEST['Customer_Mobno']);
                    $handle4->bindValue(2, $_REQUEST['Wallet']);
                    $handle4->bindValue(3, $comment);
                    $result4 = $handle4->execute();

                }
                else {
                    $handle->bindValue(15, 0);
                }

                //update customer mobno
                if($_REQUEST['New_Mobno']==null||$_REQUEST['New_Mobno']==$_REQUEST['Customer_Mobno']) {
                    $handle->bindValue(16, $_REQUEST['Customer_Mobno']);
                }
                else {

                    $handle->bindValue(16, $_REQUEST['New_Mobno']);

                    $handle2 = $this->db->prepare("update order_details set Customer_Mobno=? where Customer_Mobno=?");
                    $handle2->bindValue(1, $_REQUEST['New_Mobno']);
                    $handle2->bindValue(2, $_REQUEST['Customer_Mobno']);
                    $result2 = $handle2->execute();

                    $handle2 = $this->db->prepare("update transactions set Customer_Mobno=? where Customer_Mobno=?");
                    $handle2->bindValue(1, $_REQUEST['New_Mobno']);
                    $handle2->bindValue(2, $_REQUEST['Customer_Mobno']);
                    $result2 = $handle2->execute();
                    $_REQUEST['Account_Status']=static::$messages['ReVerification'];
                    $errresult['Message'] = $errresult['Message'].' '.static::$messages['Mobile_Updated'].' '.static::$messages['Admin_ReVerify'];

                }
                $handle->bindValue(13, $_REQUEST['Account_Status']);
                $result = $handle->execute();
           
                $handle2 = $this->db->prepare('Select * from customer_details where Id=?');
                $handle2->bindValue(1, $id);
                $handle2->execute();
                $data = $handle2->fetchAll();
                $errresult['Data'] = $data;
              
            }
           
            else {
                $errresult['Resultcode'] = static::$messages['Resultcode_1'];
                $errresult['Message'] = static::$messages['Check_Password'];
                $errresult['Data'] = static::$messages['No_Data'];
            }

        }
        else {          
            $errresult['Resultcode'] = static::$messages['Resultcode_1']; 
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle1->errorCode();
        $this->db->commit();
        $this->db = null;

        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    }

    public function setCustomerStatus($request, $response)
    {

        $args = $request->getParsedBody();

        $this->db->beginTransaction();
 
        $_REQUEST['Account_Status']=$args['status'];
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Wallet']=$args['wallet'];

        $handle1 = $this->db->prepare('Select * from customer_details where Customer_Mobno=?');
        $handle1->bindValue(1, $_REQUEST['Customer_Mobno']);
        $handle1->execute();

        $data = $handle1->fetchAll();
        $arr = array_values($data);

        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        $sms_type=null;
        if($data) {

            $errresult['Message'] = static::$messages['Data_true'];

           $handle = $this->db->prepare('update customer_details set Account_Status =?,Wallet=? where Customer_Mobno=?');
   
            $handle->bindValue(1, $_REQUEST['Account_Status']);
            $handle->bindValue(2, $_REQUEST['Wallet']);
            $handle->bindValue(3, $_REQUEST['Customer_Mobno']);
            
            if($_REQUEST['Account_Status']!=$arr[0]['Account_Status']) {
                $errresult['Message'] = $errresult['Message'].' '.static::$messages['Account_Status_Updated'];
                $sms_type="change_account_status";   
                $sms_data=array($args['status'],null,null); 
             
 
            }
            if($_REQUEST['Wallet']!=$arr[0]['Wallet']) {
                $errresult['Message'] = $errresult['Message'].' '.static::$messages['Wallet_Updated'];

                $sms_type="wallet_updated_by_customer"; 
                $sms_data=array($args['wallet'],null,null);                   
            }
    
            $result = $handle->execute();

            $result1= $handle1->execute();
            $data = $handle1->fetchObject();
  
            $errresult['Data'] = $data;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle1->errorCode();

        $sms_number=$data->Customer_Mobno;
        if($sms_type!=null)$this->testsms($sms_number,$sms_type,$sms_data);
        
        $this->db->commit();
        $this->db = null;
      
        return $response->withJson($errresult);
     
    }
}