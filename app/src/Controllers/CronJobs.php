<?php

namespace App\Controllers;

class CronJobs extends Controller
{

    public function allCronJobs($request, $response)
    {
        $this->referralcodeProcessor();
        $this->deleteExpiredFestivalPromocodes();
        $this->deleteExpiredOtherPromocodes();
        $this->deleteExpiredPromocodes();
    }
	public function referralcodeProcessor()
	{
        $this->logger->info(static::$messages['Cron_Job_referralcodeProcessor_Start']); 
		$this->db->beginTransaction();

        $sql = "SELECT Customer_Mobno from  customer_details 
                where referral_code in (
                    select referree_code 
                    from ( select * from customer_details) As Cust 
                        where Cust.isRefProcessed='I')";

        $handle = $this->db->prepare($sql);
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Customer_Grid'] = $data;
         if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'];
            $errresult['Data'] = static::$messages['No_Data'];
            $this->logger->write(static::$messages['Data_false']);
        }
        $deleted=0;
                   
        for( $i = 0; $i<count($data); $i++ ) {
        
            $mobno= $data[$i]['Customer_Mobno'];

            $comment = static::$messages['ReferalCode_100'];
            $this->insertTransaction($mobno,100,null,$comment);
                

        }
                                
		$entry_handle = $this->db->prepare("UPDATE customer_details  
    						SET Wallet = Wallet+100 
    						where referral_code in (
    							select referree_code 
    							from ( select * from customer_details) As Cust 
    							where Cust.isRefProcessed='I'
    							)"
    						);

	 	$entry_result = $entry_handle->execute();
         $this->logger->write(static::$messages['ReferalCode_100']." for ".$entry_handle->rowCount()." Customers.");

		$entry_handle = $this->db->prepare("UPDATE customer_details SET isRefProcessed='Y'  where isRefProcessed='I'");
 		$entry_result = $entry_handle->execute();
   		
        $this->logger->write($entry_handle->rowCount().static::$messages['Customers_Updated']);
   		$this->db->commit();
     //   $this->db = null;
 
        $this->logger->info(static::$messages['Cron_Job_referralcodeProcessor_End']); 
 
        $errresult['StatusCode'] = $handle->errorCode();
 
    //      // return $response
     //       ->withHeader('Content-Type', 'application/json')
       //     ->write(json_encode($errresult,JSON_PRETTY_PRINT));

        
	}

   

    public function deleteExpiredPromocodes()
    {
        $this->logger->info(static::$messages['Cron_Job_deleteExpiredPromocode_Start']); 
        
        $this->db->beginTransaction();
        $sql = 'SELECT  Name from promocodes where Valid_to < NOW()';

        $handle = $this->db->prepare($sql);
         $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Promo_Names'] = $data;
  
        $handle = $this->db->prepare("DELETE from promocodes where Valid_to < NOW()");

        $result = $handle->execute();
               
        $this->logger->write($handle->rowCount().static::$messages['Promocodes_Deleted_Promocodes']);

           $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'];
            $errresult['Data'] = static::$messages['No_Data'];
            $this->logger->write(static::$messages['Data_false']);
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db->commit();
        $this->db = null;
        
        $this->logger->info(static::$messages['Cron_Job_deleteExpiredPromocode_End']); 
   //       return $response
     //       ->withHeader('Content-Type', 'application/json')
       //     ->write(json_encode($errresult,JSON_PRETTY_PRINT));

        
    }

    public function deleteExpiredFestivalPromocodes()
    {
        $this->logger->info(static::$messages['Cron_Job_deleteExpiredFestivalPromocode_Start']); 
        
        $this->db->beginTransaction();

        $sql = 'SELECT  Name from promocodes where Valid_to < NOW() and Criteria = ?';

        $criteria = static::$messages['Criteria_All'];
        $handle = $this->db->prepare($sql);
        $handle->bindValue(1, $criteria);
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Promo_Names'] = $data;
  
        $deleted=0;
                   
        for( $i = 0; $i<count($data); $i++ ) {
        
            $name= $data[$i]['Name'];

            $handle1 = $this->db->prepare('update customer_details set Applicable_Promocodes = trim(replace(Applicable_Promocodes,?,?)) , Used_Promocodes = trim(replace(Used_Promocodes,?,?)) where Applicable_Promocodes like ? OR Used_Promocodes like ?');

            $handle1->bindValue(1, $name);
            $handle1->bindValue(2, "");
            $handle1->bindValue(3, $name);
            $handle1->bindValue(4, "");
            $handle1->bindValue(5, '%'.$name.'%');
            $handle1->bindValue(6, '%'.$name.'%');
            $result1 = $handle1->execute();
                
            $count = $handle1->rowCount();
            $deleted=$deleted+$count;
            $this->logger->write($count." ".$name.static::$messages['Promocodes_Deleted_CustomerDetails']);
        }

        $dataSend['Total_Delete']=$deleted;
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'];
            $errresult['Data'] = static::$messages['No_Data'];
            $this->logger->write(static::$messages['Data_false']);
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db->commit();
   //     $this->db = null;

        $this->logger->info(static::$messages['Cron_Job_deleteExpiredFestivalPromocode_End']); 
       
    //    return $response
      //      ->withHeader('Content-Type', 'application/json')
        //    ->write(json_encode($errresult,JSON_PRETTY_PRINT));

    
    }
    public function deleteExpiredOtherPromocodes()
    {
        $this->logger->info(static::$messages['Cron_Job_deleteExpiredOtherPromocode_Start']); 
        
        $this->db->beginTransaction();

        $sql = 'SELECT  Name from promocodes where Valid_to < NOW() and Criteria NOT IN  (?)';

        $criteria = static::$messages['Criteria_All'];
        $handle = $this->db->prepare($sql);
        $handle->bindValue(1, $criteria);
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Promo_Names'] = $data;
  
        $deleted=0;
                   
        for( $i = 0; $i<count($data); $i++ ) {
        
            $name= $data[$i]['Name'];

            $handle1 = $this->db->prepare('update customer_details set Applicable_Promocodes = trim(replace(Applicable_Promocodes,?,?)) where Applicable_Promocodes like ?');

            $handle1->bindValue(1, $name);
            $handle1->bindValue(2, "");
            $handle1->bindValue(3, '%'.$name.'%');
            $result1 = $handle1->execute();
                
            $count = $handle1->rowCount();
            $deleted=$deleted+$count;
            $this->logger->write($count." ".$name.static::$messages['Promocodes_Deleted_CustomerDetails']);
        }

        $dataSend['Total_Delete']=$deleted;
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'];
            $errresult['Data'] = static::$messages['No_Data'];
            $this->logger->write(static::$messages['Data_false']);
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db->commit();
    //    $this->db = null;

        $this->logger->info(static::$messages['Cron_Job_deleteExpiredOtherPromocode_End']); 
       
    //    return $response
      //      ->withHeader('Content-Type', 'application/json')
        //    ->write(json_encode($errresult,JSON_PRETTY_PRINT));

    
    }

    
}

?>