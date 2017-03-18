<?php

namespace App\Controllers;

class ApplicationParameters extends Controller
{
	public function getApplicationParameters($request, $response)
    {
    	$errresult['Resultcode'] = static::$messages['Resultcode_0'];;

    	 $data = array
                (
                    "Pincodes" => $this->properties['app_config']['pincodes'],
                    "Wallet_Threshold" => $this->properties['app_config']['wallet_threshold'],
                );
                 
        if($data) {

            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $data;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        return $response
        	->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
 
    }
}