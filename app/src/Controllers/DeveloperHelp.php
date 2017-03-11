<?php

namespace App\Controllers;

class DeveloperHelp extends Controller
{
	public function test($request, $response)
    {
    	echo "in DeveloperHelp";
    }
    public function readProperties($request, $response)
    {
        define('BIRD', 'Dodo bird');
        echo "hi";
        // Parse without sections
        $path = __DIR__ .'/../../sample.ini';
        $ini_array = parse_ini_file($path);
        print_r($ini_array);
echo "<br>======</br>";
echo $ini_array['path'];
echo "<br>======</br>";
    echo $ini_array['phpversion'][0];
echo "<br>======</br>";

        // Parse with sections
        $ini_array = parse_ini_file($path, true);
        print_r($ini_array);

        echo "<br>======</br>";
        echo $ini_array['second_section']['path'];
echo "<br>======</br>";
      echo $ini_array['third_section']['phpversion'][0];
echo "<br>======</br>";
echo $ini_array['third_section']['urls']['svn'];
echo "<br>======</br>";


        $path = __DIR__ .'/../../properties.ini';
        $properties = parse_ini_file($path);
            
        echo $properties['server_version'];
        echo $properties['apk_version'];

        foreach ($properties['admins'] as $admin) {
            echo $admin;
        }


        print_r($properties);

    }
    
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

}