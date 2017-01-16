<?php

namespace App\Controllers;

use \Silalahi\Slim\Logger as Logger;
use \Interop\Container\ContainerInterface as ContainerInterface;
use \PDO as PDO;
use \stdClass as stdClass;

class Controller
{
	protected $db;
    protected $logger;

    protected static $messages = [
        
        //General 
        'Data_false' => 'No Records Found.',
        'Data_true'=> 'Data Retrieved Successfully.',


        //'No_Data'=> '{}',
//'No_Data'=>  array(),
'No_Data'=>  null,


        'Resultcode_0' => 0,
        'Resultcode_1' => 1,

        //CustomerDetails Table 
        'Admin' => 'Admin',
        'Customer' => 'Customer',
        'Active' => 'Active',
        'Waiting_For_Verification' => 'Waiting For Verification',

        'Check_Mobile' => 'Please Check the Mobile Number.',
        'Account_Status_Updated' => 'Account Status Updated.', 
        'Wallet_Updated' => 'Wallet Updated.',
        'Account_Status_Not_Active' => 'Account Status Not Active.', 
        'Password_Updated' => 'Password Updated.',
        'Customer_Created' => 'Welcome to Impressive Application. Your Profile has been created.',
        'Check_Category' => 'Please Check the Category.',
        'Check_Password' => 'Mismatch in Password. You are not Authenticated.',
        'Mobile_Updated' => 'Customer Mobile Number Updated.',
        'Check_Order_Id' => 'Please Check the Order Id.',
        'Pickup_Slot_Updated' => 'Pickup Slot Updated.',
        'Delivery_Slot_Updated' => 'Delivery Slot Updated.',


        //  OrderDetails
        'Closed' => 'Closed',
        'Cancelled' => 'Cancelled',

        'Check_Display' =>'Give correct value for display (complete, today, 1,2,...,12).',
        'No_Orders_Complete' => 'There are no Orders.',
        'No_Orders_Today' => 'No Orders for Today.',
        'No_Orders_Month' => 'No Orders in Month of ',

        'Order_Created' => 'Your Order has been Created Successfully.',
        'Order_Id' => 'Your Order Id is ',
       
        'Promocode_Used' => 'Promocode has been used.',
        'Timeslot_Updated' => 'Timeslot Updated.',

        // TimeSlots Table
        'No_Slots' => 'No Slots on or after ',

        //Promocodes
        'Promocode_Created' => 'Promocode has been Created.',
        'Criteria_All' => 'All',
        'Festival' => 'Festival',
        'Customers_Applicable_Promocode' => ' Customers can use Promocode ',

        //Transaction Messages
        'Amount_Rs' => 'Amount Rs.',
        'Amount_Debit' => 'Debited against Order Id: ',
        'Amount_Credit' => 'Credited towards Order.',
        'No_Transactions_Complete' => 'There are no Transactions.',
        'No_Transactions_Today' => 'No Transactions for Today.',
        'No_Transactions_Month' => 'No Transactions in Month of ',

        //Cron Job
        'Cron_Jobs_Started' => 'All Cron Jobs Started.',
        'Cron_Jobs_Ended' => 'All Cron Jobs Ended.',
        'Customers_Updated' => ' Customers Updated.',
        'Promocodes_Deleted_CustomerDetails' => ' Promocodes Deleted from CustomerDetails Table.',
        'Promocodes_Deleted_Promocodes' => ' Promocodes Deleted from Promocodes Table.',

        
        'Cron_Job_referralcodeProcessor_Start' =>'Cron Job referralcodeProcessor Started.',
        'Cron_Job_referralcodeProcessor_End' =>'Cron Job referralcodeProcessor Ended.',
        'Cron_Job_deleteExpiredFestivalPromocode_Start' =>'Cron Job deleteExpiredFestivalPromocode Started.',
        'Cron_Job_deleteExpiredFestivalPromocode_End' =>'Cron Job deleteExpiredFestivalPromocode Ended.',

        'Cron_Job_deleteExpiredPromocode_Start' =>'Cron Job deleteExpiredPromocode Started.',
        'Cron_Job_deleteExpiredPromocode_End' =>'Cron Job deleteExpiredPromocode Ended.',

        'Cron_Job_deleteExpiredOtherPromocode_Start' =>'Cron Job deleteExpiredOtherPromocode Started.',
        'Cron_Job_deleteExpiredOtherPromocode_End' =>'Cron Job deleteExpiredOtherPromocode Ended.',

        // ReferalCode
        'ReferalCode_100' => 'Rs. 100 Added to your Wallet for using Referal code on creating Account.',

    ];

    public function __construct(Logger $logger, PDO $db)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function insertTransaction($customer_Mobno,$amount,$order_id,$comment)
    {

        $handle = $this->db->prepare("insert into transactions (Customer_Mobno,Amount,Order_id,Comment) values (?,?,?,?)");

        $handle->bindValue(1, $customer_Mobno);
        $handle->bindValue(2, $amount);
        $handle->bindValue(3, $order_id);
        $handle->bindValue(4, $comment);
        $result = $handle->execute();
    }

    public function insertSMSNotification($errorCode, $errorMessage, $jobId, $number,$text)
    {
 //$this->db->beginTransaction();
        $handle = $this->db->prepare("insert into notification (ErrorCode,ErrorMessage,JobId,Number,Text) values (?,?,?,?,?)");

        $handle->bindValue(1, $errorCode);
        $handle->bindValue(2, $errorMessage);
        $handle->bindValue(3, $jobId);
        $handle->bindValue(4, $number);
        $handle->bindValue(5, $text);
        $result = $handle->execute();
    }


    public function testsms($number,$type,$data)
    {

   //     Dear ##Field##, your password is ##Field##.

       

          $template = array(
            "Dear ".$data.", your password is ".$data.".",
            "Template 2",
            "Template 3",
            "Template 4"
            );
        $msg = $template[$type];
        

        $output['ErrorCode']="000";
        $output["ErrorMessage"]= "Success";
        $output["JobId"]= "3860574";
        $output["MessageData"] =array(
                                    array(
                                       "Number" => "919884873929",
                                       "MessageParts" => array(
                                            array( 
                                                "MsgId" => "919884873929-7625698446e649c5a19d463e7be0b981",
                                                "PartId" => 1,
                                                "Text" => $msg
                                                        
        ))
        ));
        $this->insertSMSNotification($output["ErrorCode"],
                                    $output["ErrorMessage"],
                                    $output["JobId"],
                                    $output["MessageData"][0]["Number"],
                                    $output["MessageData"][0]["MessageParts"][0]["Text"]);

        if($output['ErrorCode']=="000")
            $this->logger->info("sms sent successfully");
        else 
            $this->logger->info("failed");

         // var_dump($output);
       /*     return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($output,JSON_PRETTY_PRINT));
       */     //return 
            echo json_encode($output,JSON_PRETTY_PRINT);
    
    }

    public function sendNewSMS($number,$type,$data)
    {
       /* $template = array(
            "Your user profile was successfully created in impressive application with id ".$data.".Login into our appplication for more details",
            "Your worker profile was successfully created in impressive application with id ".$data.".Login into our appplication for more details",
            "Your order was created successfully in impressive application.you can track your order in our application with id ".$data."",
            "You are assigned with order ".$data.".Check the order details in application",
            "Your order ".$data." was completed and now it is waiting for delivery");
*/
$this->db->beginTransaction();
          $template = array(
            "Dear ".$data.", your password is ".$data.".",
            "Template 2",
            "Template 3",
            "Template 4"
            );
        // Replace with your username
        $user = "Impressive application";
        $user  = urlencode($user);
        // Replace with your API KEY (We have sent API KEY on activation email, also available on panel)
        $password = "rahul@19";
        // Replace with the destination mobile Number to which you want to send sms
        $msisdn = $number;
        // Replace if you have your own Six character Sender ID, or check with our support team.
        $sid = "SMSHUB";
        // Replace with client name
        $name = "Arun Kumar";
        // Replace if you have OTP in your template.
        // Replace with your Message content
        $msg = $template[$type];
      //  $msg = "Thank you for contacting with us. We will get back to you soon";
        $msg  = urlencode($msg);
        // Keep 0 if you don’t want to flash the message
        $fl = "0";
        // if you are using transaction sms api then keep gwid = 2 or if promotional then remove this parameter
        $gwid = "2";
        // For Plain Text, use "txt" ; for Unicode symbols or regional Languages like hindi/tamil/kannada use "uni"
        $smsrl = "http://cloud.smsindiahub.in/vendorsms/pushsms.aspx?user=".$user."&password=".$password."&msisdn=".$msisdn."&sid=".$sid."&msg=".$msg."&fl=".$fl."&gwid=".$gwid."";
        //$smsrl = "http://cloud.smsindiahub.in/vendorsms/pushsms.aspx?user=Impressive application&password=rahul@19&msisdn=9981188868&sid=SMSHUB&msg=Thank you for contacting with us. We will get back to you soon&fl=0&gwid=2";
        echo $smsrl;
        $ch = curl_init($smsrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          $this->logger->info("before output");
        $output = curl_exec($ch);
        $this->logger->info("after output");
   //     $this->logger->info("after output".$output);

        $this->insertSMSNotification($output["ErrorCode"],
            $output["ErrorMessage"],
            $output["JobId"],
            $output["MessageData"][0]["Number"],
           $output["MessageData"][0]["MessageParts"][0]["Text"]);


        if($output['ErrorCode']=="000")
            $this->logger->info("SMS sent successfully from smshub");
        else 
            $this->logger->info("failed from sms hub");
   $this->db->commit();
        $this->db = null;

        echo $output;
        curl_close($ch);
        // Display MSGID of the successful sms push
        echo $output;
        $this->db->commit();
        $this->db = null;

}
   

/*
	public function __get($property)
	{
		if($this->container->{$property}){

			return $this->container->{$property};
		}
		
	}
*/	

	

	
}
