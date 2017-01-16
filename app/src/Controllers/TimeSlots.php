<?php

namespace App\Controllers;

class TimeSlots extends Controller
{
	public function availableSlots($request, $response)
	{
		//$args = $request->getQueryParams();
        $args = $request->getParsedBody();
 		
 		$handle = $this->db->prepare('Select * from time_slots where Slot_Date>= :date');
   
  		$today = date("Y-m-d");
		$date = strtotime($args['date']);
		$current_date = date('Y-m-d',$date);
		$handle->bindParam('date', $current_date);

    	$result = $handle->execute();
		
		$data = $handle->fetchAll();
  	 	$dataSend['Available_Slots'] = $data;
     
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['No_Slots'].date('d-m-Y',$date).'.';
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
//        return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));

	}

    public function updateTimeSlot($request, $response)
    {

        $args = $request->getParsedBody();
    
        $this->db->beginTransaction();
 
        $handle = $this->db->prepare('replace into time_slots (Slot_1,Slot_2, Slot_3,Slot_4,Slot_5,Slot_6, Slot_7,Slot_8,Slot_9,Slot_10,
                                                    Slot_11,Slot_12, Slot_13,Slot_14,Slot_15,Slot_16, Slot_17,Slot_18,Slot_19,Slot_20,
                                                    Slot_21,Slot_22, Slot_23,Slot_24,Slot_25,Slot_26, Slot_27,Slot_28,Slot_29,Slot_30,
                                                    Slot_31,Slot_32, Slot_33,Slot_34,Slot_35,Slot_36, Slot_37,Slot_38,Slot_39,Slot_40,
                                                    Slot_41,Slot_42, Slot_43,Slot_44,Slot_45,Slot_46, Slot_47,Slot_48,Slot_Date) values(
                                                    ?,?,?,?,?,?,?,?,?,?,
                                                    ?,?,?,?,?,?,?,?,?,?,
                                                    ?,?,?,?,?,?,?,?,?,?,
                                                    ?,?,?,?,?,?,?,?,?,?,
                                                    ?,?,?,?,?,?,?,?,?)');

        $_REQUEST['Slot_1']=$args['slot1'];
        $_REQUEST['Slot_2']=$args['slot2'];
        $_REQUEST['Slot_3']=$args['slot3'];
        $_REQUEST['Slot_4']=$args['slot4'];
        $_REQUEST['Slot_5']=$args['slot5'];
        $_REQUEST['Slot_6']=$args['slot6'];
        $_REQUEST['Slot_7']=$args['slot7'];
        $_REQUEST['Slot_8']=$args['slot8'];
        $_REQUEST['Slot_9']=$args['slot9'];
        $_REQUEST['Slot_10']=$args['slot10'];
        $_REQUEST['Slot_11']=$args['slot11'];
        $_REQUEST['Slot_12']=$args['slot12'];
        $_REQUEST['Slot_13']=$args['slot13'];
        $_REQUEST['Slot_14']=$args['slot14'];
        $_REQUEST['Slot_15']=$args['slot15'];
        $_REQUEST['Slot_16']=$args['slot16'];
        $_REQUEST['Slot_17']=$args['slot17'];
        $_REQUEST['Slot_18']=$args['slot18'];
        $_REQUEST['Slot_19']=$args['slot19'];
        $_REQUEST['Slot_20']=$args['slot20'];
        $_REQUEST['Slot_21']=$args['slot21'];
        $_REQUEST['Slot_22']=$args['slot22'];
        $_REQUEST['Slot_23']=$args['slot23'];
        $_REQUEST['Slot_24']=$args['slot24'];
        $_REQUEST['Slot_25']=$args['slot25'];
        $_REQUEST['Slot_26']=$args['slot26'];
        $_REQUEST['Slot_27']=$args['slot27'];
        $_REQUEST['Slot_28']=$args['slot28'];
        $_REQUEST['Slot_29']=$args['slot29'];
        $_REQUEST['Slot_30']=$args['slot30'];
        $_REQUEST['Slot_31']=$args['slot31'];
        $_REQUEST['Slot_32']=$args['slot32'];
        $_REQUEST['Slot_33']=$args['slot33'];
        $_REQUEST['Slot_34']=$args['slot34'];
        $_REQUEST['Slot_35']=$args['slot35'];
        $_REQUEST['Slot_36']=$args['slot36'];
        $_REQUEST['Slot_37']=$args['slot37'];
        $_REQUEST['Slot_38']=$args['slot38'];
        $_REQUEST['Slot_39']=$args['slot39'];
        $_REQUEST['Slot_40']=$args['slot40'];
        $_REQUEST['Slot_41']=$args['slot41'];
        $_REQUEST['Slot_42']=$args['slot42'];
        $_REQUEST['Slot_43']=$args['slot43'];
        $_REQUEST['Slot_44']=$args['slot44'];
        $_REQUEST['Slot_45']=$args['slot45'];
        $_REQUEST['Slot_46']=$args['slot46'];
        $_REQUEST['Slot_47']=$args['slot47'];
        $_REQUEST['Slot_48']=$args['slot48'];
        
        $_REQUEST['Slot_Date']=$args['slotdate'];

        $date = strtotime($_REQUEST['Slot_Date']);
        $current_date = date('Y-m-d',$date);

        $handle->bindValue(1, $_REQUEST['Slot_1']+0);
        $handle->bindValue(2, $_REQUEST['Slot_2']+0);
        $handle->bindValue(3, $_REQUEST['Slot_3']+0);
        $handle->bindValue(4, $_REQUEST['Slot_4']+0);
        $handle->bindValue(5, $_REQUEST['Slot_5']+0);
        $handle->bindValue(6, $_REQUEST['Slot_6']+0);
        $handle->bindValue(7, $_REQUEST['Slot_7']+0);
        $handle->bindValue(8, $_REQUEST['Slot_8']+0);
        $handle->bindValue(9, $_REQUEST['Slot_9']+0);
        $handle->bindValue(10, $_REQUEST['Slot_10']+0);
        $handle->bindValue(11, $_REQUEST['Slot_11']+0);
        $handle->bindValue(12, $_REQUEST['Slot_12']+0);
        $handle->bindValue(13, $_REQUEST['Slot_13']+0);
        $handle->bindValue(14, $_REQUEST['Slot_14']+0);
        $handle->bindValue(15, $_REQUEST['Slot_15']+0);
        $handle->bindValue(16, $_REQUEST['Slot_16']+0);
        $handle->bindValue(17, $_REQUEST['Slot_17']+0);
        $handle->bindValue(18, $_REQUEST['Slot_18']+0);
        $handle->bindValue(19, $_REQUEST['Slot_19']+0);
        $handle->bindValue(20, $_REQUEST['Slot_20']+0);
        $handle->bindValue(21, $_REQUEST['Slot_21']+0);
        $handle->bindValue(22, $_REQUEST['Slot_22']+0);
        $handle->bindValue(23, $_REQUEST['Slot_23']+0);
        $handle->bindValue(24, $_REQUEST['Slot_24']+0);
        $handle->bindValue(25, $_REQUEST['Slot_25']+0);
        $handle->bindValue(26, $_REQUEST['Slot_26']+0);
        $handle->bindValue(27, $_REQUEST['Slot_27']+0);
        $handle->bindValue(28, $_REQUEST['Slot_28']+0);
        $handle->bindValue(29, $_REQUEST['Slot_29']+0);
        $handle->bindValue(30, $_REQUEST['Slot_30']+0);
        $handle->bindValue(31, $_REQUEST['Slot_31']+0);
        $handle->bindValue(32, $_REQUEST['Slot_32']+0);
        $handle->bindValue(33, $_REQUEST['Slot_33']+0);
        $handle->bindValue(34, $_REQUEST['Slot_34']+0);
        $handle->bindValue(35, $_REQUEST['Slot_35']+0);
        $handle->bindValue(36, $_REQUEST['Slot_36']+0);
        $handle->bindValue(37, $_REQUEST['Slot_37']+0);
        $handle->bindValue(38, $_REQUEST['Slot_38']+0);
        $handle->bindValue(39, $_REQUEST['Slot_39']+0);
        $handle->bindValue(40, $_REQUEST['Slot_40']+0);
        $handle->bindValue(41, $_REQUEST['Slot_41']+0);
        $handle->bindValue(42, $_REQUEST['Slot_42']+0);
        $handle->bindValue(43, $_REQUEST['Slot_43']+0);
        $handle->bindValue(44, $_REQUEST['Slot_44']+0);
        $handle->bindValue(45, $_REQUEST['Slot_45']+0);
        $handle->bindValue(46, $_REQUEST['Slot_46']+0);
        $handle->bindValue(47, $_REQUEST['Slot_47']+0);
        $handle->bindValue(48, $_REQUEST['Slot_48']+0);
      
        $handle->bindValue(49, $current_date);
        
        $result = $handle->execute();
       
        $handle = $this->db->prepare('Select * from time_slots where slot_date = :date');
        $handle->bindParam('date', $current_date);
        $handle->execute();
        $data = $handle->fetchAll();
 
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;
        $errresult['Message'] = static::$messages['Data_true'].' '.static::$messages['Timeslot_Updated'];
        
        $errresult['Data'] = $data;
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db->commit();            
        $this->db = null;
      
      //  return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));

    }
}
