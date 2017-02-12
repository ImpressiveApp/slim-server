<?php

namespace App\Controllers;

class Transactions extends Controller
{		
	public function getTransactionHistory($request, $response)
	{
		 // $args = $request->getQueryParams();
       $args = $request->getParsedBody();

      $entry_handle = $this->db->prepare('Select * from customer_details where Customer_Mobno=:Customer_Mobno LIMIT 1');
          
      $entry_handle->bindParam('Customer_Mobno', $args['mobno']);

      $result = $entry_handle->execute();
      $data = $entry_handle->fetchObject();
 
      $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

      if($data) {

          if($args['display']=="complete") { 
              $handle = $this->db->prepare('select * from transactions where Customer_Mobno = :Customer_Mobno');
               $handle->bindParam('Customer_Mobno', $args['mobno']);
          }
          // today
          else if($args['display']=="today") {

              $today = date("Y-m-d");
              $errresult['Mess']= $today;
              
              $handle = $this->db->prepare('select * from transactions where Transaction_Date > :display and Customer_Mobno = :Customer_Mobno');
              $handle->bindParam('display', $today);
              $handle->bindParam('Customer_Mobno', $args['mobno']);
           }
          //FOR MONTH
          else {  
              $handle = $this->db->prepare("select * from transactions where  DATE_FORMAT(Transaction_Date,'%c') = :display and Customer_Mobno = :Customer_Mobno");
              $handle->bindParam('display', $args['display']);
                $handle->bindParam('Customer_Mobno', $args['mobno']);
            } 

            $result = $handle->execute();
             $this->logger->write($handle->rowCount());
            $data1 = $handle->fetchAll();
            $dataSend['Order_List'] = $data1;
         //    $errresult['Mess1']= $data1;

            if($data1) {

                $errresult['Message'] = static::$messages['Data_true'];
                $errresult['Data'] = $dataSend;
            }
            else {

                $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Display'];

                if($args['display']=="complete")
                    $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['No_Transactions_Complete'];
        
                else if($args['display']=="today")
                    $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['No_Transactions_Today'];
        
                else if($args['display']>=1 && $args['display']<=12)
                    $errresult['Message'] = static::$messages['No_Transactions_Month'].date("F Y", mktime(null, null, null, $args['display'], 1)).'.';
                
                $errresult['Resultcode'] = static::$messages['Resultcode_1'];
                $errresult['Data'] = static::$messages['No_Data'];
            }

        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $entry_handle->errorCode();
        $this->db = null;
      
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));

    }

    

}
