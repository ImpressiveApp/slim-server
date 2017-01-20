<?php

namespace App\Controllers;



class RateCard extends Controller
{

    public function sms($request, $response)
    {
        $number=9884873929;
        $type="Create_New_Customer_with_Referral_Code";
        $sms_data=array("Deepak Prabakar","ZXY");
        
     //   $this->logger->info("sms sent from rate");
//       $this->testsms($number,$type,$data);
  $this->testsms($number,$type,$data);

    //    $this->sendNewSMS($number,$type,$data);

    /*    $errresult['ErrorCode']="000";
        $errresult["ErrorMessage"]= "Success";
          $errresult["JobId"]= "3860574";
          $errresult["MessageData"] =array(
                                    array(
            
                                    "Number" => "919884873929",
                                  "MessageParts" => array(array ( 
        
          "MsgId" => "919884873929-7625698446e649c5a19d463e7be0b981",
          "PartId" => 1,
          "Text" => "Thank you for contacting with us. We will get back to you soon"
        
        ))
        ));
  */
      /*   return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
      */  

            $json = '
{
    "type": "donut",
    "name": "Cake",
    "toppings": [
        { "id": "5002", "type": "Glazed" },
        { "id": "5006", "type": "Chocolate with Sprinkles" },
        { "id": "5004", "type": "Maple" }
    ]
}';

$yummy = json_decode($json);

//echo($yummy);

$json = '
{
    "type": "donut",
    "name": "Cake",
    "toppings": [
        { "id": "5002", "type": "Glazed" },
        { "id": "5006", "type": "Chocolate with Sprinkles" },
        { "id": "5004", "type": "Maple" }
    ]
}';

$yummy = json_decode($json, true);

var_dump( $yummy);
//echo $yummy['toppings'][2]['type'];
    }
	public function getRateCard($request, $response)
    {
       // $this->db->beginTransaction();
        $sql = 'select * from ratecard';

        $handle = $this->db->prepare($sql);
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Ratelist'] = $data;
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
    //    return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));

    
    }
    public function updateRateCard($request, $response)
    {

        $args = $request->getParsedBody();
        
        $this->db->beginTransaction();

        $_REQUEST['category']=$args['category'];
        $_REQUEST['price']=$args['price'];
        $_REQUEST['action']=$args['action'];

        $entry_handle = null;
        if($_REQUEST['action']=="I") {
            $entry_handle = $this->db->prepare('insert into ratecard(category,price) values(?,?)');
            $entry_handle->bindValue(1, $_REQUEST['category']);
            $entry_handle->bindValue(2, $_REQUEST['price']);
        }
        else if($_REQUEST['action']=="U") {
            $entry_handle = $this->db->prepare('update ratecard set price=? where category = ?');
            $entry_handle->bindValue(1, $_REQUEST['price']);
            $entry_handle->bindValue(2, $_REQUEST['category']);
        }
        else {
            $entry_handle = $this->db->prepare('delete from ratecard where category=?');
            $entry_handle->bindValue(1, $_REQUEST['category']);
        }
        
       
        $result = $entry_handle->execute();
        $this->db->commit();
    
        $this->getRateCard($request, $response);

    }

}