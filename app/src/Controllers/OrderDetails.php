<?php

namespace App\Controllers;

class OrderDetails extends Controller
{

   	public function getAllOpenOrders($request, $response)
	{
		$this->db->beginTransaction();

        $sql = 'Select * from order_details where Order_status not in (?,?) order by Pickup_Slot,Delivery_Slot';

        $handle = $this->db->prepare($sql);
 	    $handle->bindValue(1, static::$messages['Closed']);
    	$handle->bindValue(2, static::$messages['Cancelled']);

    	$this->logger->info('|SQL> '.$sql);
 	   	$result = $handle->execute();
		$data = $handle->fetchAll();
        $dataSend['Order_Grid'] = $data;
		
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

    public function getOpenOrders($request, $response)
    {
       // $args = $request->getQueryParams();
         $args = $request->getParsedBody();

        $this->db->beginTransaction();

        $sql = 'Select * from order_details where Order_status not in (?,?) and Customer_Mobno = ? order by Pickup_Slot,Delivery_Slot';

        $handle = $this->db->prepare($sql);
        $handle->bindValue(1, static::$messages['Closed']);
        $handle->bindValue(2, static::$messages['Cancelled']);
        $handle->bindValue(3, $args['mobno']);

        $this->logger->info('|SQL> '.$sql);
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Orders_Grid'] = $data;
        
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
	
	public function getOrderHistory($request, $response)
	{
		//$args = $request->getQueryParams();
         $args = $request->getParsedBody();
    
		if($args['display']=="complete")
   			$handle = $this->db->prepare('select * from order_details');

 		else if($args['display']=="today") {
 			$today1 = date("Y-m-d H:i:s");
    		$today = date("Y-m-d");

    		$handle = $this->db->prepare('select * from order_details where Created_Date > :display');
    		
    		$handle->bindParam('display', $today);
		}

 		else {  
 			$handle = $this->db->prepare("select *, DATE_FORMAT(Created_Date,'%c') from order_details where DATE_FORMAT(Created_Date,'%c') = :display");
    		$handle->bindValue('display', $args['display']);
 	   } 

	    $result = $handle->execute();
		$data = $handle->fetchAll();
        $dataSend['Order_List'] = $data;
		$errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Display'];

            if($args['display']=="complete")
            	$errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['No_Orders_Complete'];

            else if($args['display']=="today")
            	$errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['No_Orders_Today'];

 	 		else if($args['display']>=1 && $args['display']<=12)
	 			$errresult['Message'] = static::$messages['No_Orders_Month'].date("F Y", mktime(null, null, null, $args['display'], 1));

            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db = null;
      
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
	}

	public function getUserOrderHistory($request, $response)
	{
	   // $args = $request->getQueryParams();
        $args = $request->getParsedBody();
        $entry_handle = $this->db->prepare('Select * from customer_details where  
            Customer_Mobno=:Customer_Mobno LIMIT 1');
          
        $entry_handle->bindParam('Customer_Mobno', $args['mobno']);

        $result = $entry_handle->execute();
        $data = $entry_handle->fetchObject();

        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            //for complete
            if($args['display']=="complete") { 
                $handle = $this->db->prepare('select order_id,created_date, order_status,cost from order_details where Customer_Mobno = :Customer_Mobno order by order_id desc');
                $handle->bindParam('Customer_Mobno', $args['mobno']);
            }

            //today
            else if($args['display']=="today") {
                $today = date("Y-m-d");

                $handle = $this->db->prepare('select order_id,created_date, order_status ,cost from order_details where Created_Date > :display and Customer_Mobno = :Customer_Mobno order by order_id desc');
                $handle->bindParam('display', $today);
                $handle->bindParam('Customer_Mobno', $args['mobno']);
            }
            //FOR MONTH
            else {  
                $handle = $this->db->prepare("select order_id,created_date, order_status,cost from order_details where DATE_FORMAT(Created_Date,'%c')= :display and Customer_Mobno = :Customer_Mobno order  by order_id desc");
                $handle->bindParam('display', $args['display']);
                $handle->bindParam('Customer_Mobno', $args['mobno']);
            } 
        
            $result = $handle->execute();
     
            $data = $handle->fetchAll();
            
            $dataSend['Order_list'] = $data;

            $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

            if($data) {
                $errresult['Message'] = static::$messages['Data_true'];
                $errresult['Data'] = $dataSend;
            }
            else {
                $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Display'];

                if($args['display']=="complete")
                    $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['No_Orders_Complete'];

                else if($args['display']=="today")
                    $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['No_Orders_Today'];
          
                else if($args['display']>=1 && $args['display']<=12)
                    $errresult['Message'] = static::$messages['No_Orders_Month'].date("F Y", mktime(null, null, null, $args['display'], 1)).'.';
          
                $errresult['Data'] = static::$messages['No_Data'];
            }

        }
        else {
           
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $entry_handle->errorCode();
        $this->db = null;
      
        return $response->withJson($errresult);        
	}

    public function createOrder($request, $response)
    {
        $args = $request->getParsedBody();

        $this->db->beginTransaction();
 
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Order_Status']=$args['status'];
        $_REQUEST['No_Of_Items']=$args['noOfItems'];
        $_REQUEST['Pickup_Slot']=$args['pickupSlot'];
        $_REQUEST['Delivery_Slot']=$args['deliverySlot'];
        $_REQUEST['Address']=$args['address'];
        $_REQUEST['Types']=$args['type'];
        $_REQUEST['Promocode']=$args['promocode'];
        $_REQUEST['Cost']=0;
        $_REQUEST['isRefProcessed']=$args['isRefProcessed'];

        $entry_handle = $this->db->prepare('Select Id,Customer_Mobno, Account_status from customer_details where Customer_Mobno= :Customer_Mobno');
        $entry_handle->bindParam('Customer_Mobno', $args['mobno']);
        $entry_handle->execute();
        $entry_result = $entry_handle->execute();
        
        $data = $entry_handle->fetchAll();
        $arr = array_values($data);
     
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];

        if($data) {

            if($arr[0]['Account_status']=="Active") {

                $id=$arr[0]['Id'];
               
                $handle = $this->db->prepare('insert into order_details(Customer_Mobno,Order_Status,No_Of_Items,Address,Type_Of_Clothes,Cost,Pickup_Slot,Delivery_Slot,Applied_Promocode) values(?,?,?,?,?,?,?,?,?)');
 

                $value =explode("_",$_REQUEST['Pickup_Slot']);
                $pickup_slot=$value[0].'_'.$value[1];
                $pickup_date = date('Y-m-d',strtotime($value[2]));

                $value =explode("_",$_REQUEST['Delivery_Slot']);
                $delivery_slot=$value[0].'_'.$value[1];
                $delivery_date = date('Y-m-d',strtotime($value[2]));

                $handle->bindValue(1, $_REQUEST['Customer_Mobno']);
                $handle->bindValue(2, $_REQUEST['Order_Status']);
                $handle->bindValue(3, $_REQUEST['No_Of_Items']);
                $handle->bindValue(4, $_REQUEST['Address']);
                $handle->bindValue(5, $_REQUEST['Types']);
                $handle->bindValue(6, $_REQUEST['Cost']);
                $handle->bindValue(7, $_REQUEST['Pickup_Slot']);
                $handle->bindValue(8, $_REQUEST['Delivery_Slot']);
                $handle->bindValue(9, $_REQUEST['Promocode']);

                // Reducing 1 from Pickup Slot
                $handle2 = $this->db->prepare("update time_slots set ".$pickup_slot."=".$pickup_slot."-1 where Slot_Date=?");

                $handle2->bindValue(1, $pickup_date);
                $result2 = $handle2->execute();

                // Reducing 1 from Delivery Slot
                $handle3 = $this->db->prepare("update time_slots set ".$delivery_slot."=".$delivery_slot."-1 where Slot_Date=?");

                $handle3->bindValue(1, $delivery_date);
                $result3 = $handle3->execute();

                // Creating Order
                $result = $handle->execute();
                $order_id = $this->db->lastInsertId();

                // If promocode is used
                if($_REQUEST['Promocode']!=null) {   
                    $promo_comment=' '.$_REQUEST['Promocode'].' '.static::$messages['Promocode_Used'];
        
                    $handle5 = $this->db->prepare("update customer_details set Used_Promocodes=concat(IFNULL(Used_Promocodes,?),?), Applicable_Promocodes=replace(Applicable_Promocodes,?,?) where id =?");

                    $handle5->bindValue(1, "");
                    $handle5->bindValue(2, " ".$_REQUEST['Promocode']);
                    $handle5->bindValue(3, $_REQUEST['Promocode']);
                    $handle5->bindValue(4, "");
                    $handle5->bindValue(5, $id);
                    $result5 = $handle5->execute();

                }
                else
                    $promo_comment="";

                $comment= static::$messages['Order_Created'].' '.static::$messages['Order_Id'].$order_id.'.'.$promo_comment;
              
                // Inserting entry in transaction Table
   /*             $handle4 = $this->db->prepare("insert into transactions (Customer_Mobno,Amount,Order_id,Comment) values (?,?,?,?)");

                $handle4->bindValue(1, $_REQUEST['Customer_Mobno']);
                $handle4->bindValue(2, $_REQUEST['Cost']);
                $handle4->bindValue(3, $order_id);
                $handle4->bindValue(4, $comment);
                $result4 = $handle4->execute();
 */
                $this->insertTransaction($_REQUEST['Customer_Mobno'],$_REQUEST['Cost'],$order_id,$comment);
               //   $this->logger->write("IN CREATE");
                if($_REQUEST['isRefProcessed']=='N') {

                    $handle5 = $this->db->prepare("UPDATE customer_details c 
                                JOIN order_details o ON o.Customer_Mobno =  c.Customer_Mobno 
                                SET c.isRefProcessed = CASE 
                                    WHEN (
                                        select sum(cost) 
                                        from order_details 
                                        where Customer_Mobno = ".$_REQUEST['Customer_Mobno'].")>=100 
                                    THEN 'I' 
                                    ELSE c.isRefProcessed 
                                    END 
                                WHERE   c.Customer_Mobno = ".$_REQUEST['Customer_Mobno']."");
                    $result5 = $handle5->execute();
                    $count = $handle5->rowCount();
                    if($count==0)
                        $this->logger->write("Total placed orders cost < 100 for Customer_Mobno ".$_REQUEST['Customer_Mobno']);
                    else if($count > 0)
                        $this->logger->write(" isRefProcessed updated from N to I for Customer_Mobno ".$_REQUEST['Customer_Mobno']);
                }

                $handle = $this->db->prepare('Select * from order_details where order_id=?');
                $handle->bindValue(1, $order_id);
                $handle->execute();
                $data = $handle->fetchObject();
                    
                $errresult['Message'] = static::$messages['Data_true'].' '.static::$messages['Order_Id'].$order_id.'.';
                $errresult['Data'] = $data;
             
            }
            else {

                $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Account_Status_Not_Active'];
                $errresult['Data'] = static::$messages['No_Data'];
       
            } 

        }
        else {
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'];
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $entry_handle->errorCode();
        $this->db->commit();
        $this->db = null;
      
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
    }

    public function updateOrder($request, $response)
    {
        $args = $request->getParsedBody();

        $this->db->beginTransaction();

        $_REQUEST['Order_Id']=$args['orderid'];
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Order_Status']=$args['status'];
        $_REQUEST['No_Of_Items']=$args['noOfItems'];
        $_REQUEST['Pickup_Slot']=$args['pickupSlot'];
        $_REQUEST['Delivery_Slot']=$args['deliverySlot'];
        $_REQUEST['Address']=$args['address'];
        $_REQUEST['Types']=$args['type'];
        $_REQUEST['Cost']=$args['cost'];
     
        $entry_handle = $this->db->prepare('Select * from order_details where order_id=? and Customer_Mobno=?');
        $entry_handle->bindValue(1, $_REQUEST['Order_Id']);
        $entry_handle->bindValue(2, $_REQUEST['Customer_Mobno']);
        $entry_handle->execute();
        $entry_result = $entry_handle->execute();
        
        $data = $entry_handle->fetchAll();

        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {

            $errresult['Message'] = static::$messages['Data_true'];

             $arr = array_values($data);

            $Applied_Promocode=$arr[0]['Applied_Promocode'];

            $handle = $this->db->prepare('update order_details set Order_Status=?,No_Of_Items =? ,Address=?, Type_Of_Clothes =?, Cost =?, Before_Discount=?,Pickup_Slot =? ,Delivery_Slot=? where order_id=?');
 
            $cost = $_REQUEST['Cost'];
            $handle->bindValue(1, $_REQUEST['Order_Status']);
            $handle->bindValue(2, $_REQUEST['No_Of_Items']);
            $handle->bindValue(3, $_REQUEST['Address']);
            $handle->bindValue(4, $_REQUEST['Types']);

            if($Applied_Promocode==NULL) {
                $handle->bindValue(5, $_REQUEST['Cost']);                
            }
            else {
                $handle4 = $this->db->prepare("select Discount from promocodes where Name=?");
                $handle4->bindValue(1, $Applied_Promocode);
                $result4 = $handle4->execute();
                $data4 = $handle4->fetchAll();
                $arr4 = array_values($data4);

                $discount=$arr4[0]['Discount'];
                $cost= $_REQUEST['Cost'] -($_REQUEST['Cost']*$discount/100.0);
                $handle->bindValue(5, $cost);
            }

            $handle->bindValue(6, $_REQUEST['Cost']);
            $handle->bindValue(7, $_REQUEST['Pickup_Slot']);
            $handle->bindValue(8, $_REQUEST['Delivery_Slot']);
            $handle->bindValue(9, $_REQUEST['Order_Id']);
            $result = $handle->execute();

            $handle = $this->db->prepare('Select * from order_details where order_id=?');
            $handle->bindValue(1, $_REQUEST['Order_Id']);
            $handle->execute();
            $data = $handle->fetchAll();
   
            if($_REQUEST['Cost']>0&&($arr[0]['Before_Discount']!=$_REQUEST['Cost']))  {
       
                $handle2 = $this->db->prepare("update customer_details set Wallet =(Wallet +?-?) where Customer_Mobno=?");

                $handle2->bindValue(1, $arr[0]['Cost']);
                $handle2->bindValue(2, $cost);
                $handle2->bindValue(3, $_REQUEST['Customer_Mobno']);
                $result2 = $handle2->execute();

                $comment = static::$messages['Amount_Rs'].$cost.' '.static::$messages['Amount_Debit'].$_REQUEST['Order_Id'].'.';
            
             /*   $handle4 = $this->db->prepare("insert into transactions (Customer_Mobno,Amount,Order_id,Comment) values (?,?,?,?)");

                $handle4->bindValue(1, $_REQUEST['Customer_Mobno']);
                $handle4->bindValue(2, $cost);
                $handle4->bindValue(3, $_REQUEST['Order_Id']);
                $handle4->bindValue(4, $comment);
                $result4 = $handle4->execute();
*/
             //   static::$this->insertTransaction($_REQUEST['Customer_Mobno'],$cost,$_REQUEST['Order_Id'],$comment);

                $this->insertTransaction($_REQUEST['Customer_Mobno'],$cost,$_REQUEST['Order_Id'],$comment);

                $errresult['Message'] = $errresult['Message'].' '.$comment;
            }

            //update pickupslot
            if($_REQUEST['Pickup_Slot']!=$arr[0]['Pickup_Slot']) {
                    
                $value =explode("_",$_REQUEST['Pickup_Slot']);
                $pickup_slot=$value[0].'_'.$value[1];
                $pickup_date = date('Y-m-d',strtotime($value[2]));

                $handle2 = $this->db->prepare("update time_slots set ".$pickup_slot."=".$pickup_slot."-1 where Slot_Date=?");

                $handle2->bindValue(1, $pickup_date);
                $result2 = $handle2->execute();

                $value =explode("_",$arr[0]['Pickup_Slot']);
                $old_pickup_slot=$value[0].'_'.$value[1];
                $old_pickup_date = date('Y-m-d',strtotime($value[2]));

                $handle2 = $this->db->prepare("update time_slots set ".$old_pickup_slot."=".$old_pickup_slot."+1 where Slot_Date=?");

                $handle2->bindValue(1, $old_pickup_date);
                $result2 = $handle2->execute();

                $this->insertTransaction($_REQUEST['Customer_Mobno'],0,$_REQUEST['Order_Id'],static::$messages['Pickup_Slot_Updated']);
                $errresult['Message'] = $errresult['Message'].' '.static::$messages['Pickup_Slot_Updated'];
            }
            //update pickupslot
            if($_REQUEST['Delivery_Slot']!=$arr[0]['Delivery_Slot']) {
  
                $value =explode("_",$_REQUEST['Delivery_Slot']);
                $delivery_slot=$value[0].'_'.$value[1];
                $delivery_date = date('Y-m-d',strtotime($value[2]));

                $handle3 = $this->db->prepare("update time_slots set ".$delivery_slot."=".$delivery_slot."-1 where Slot_Date=?");
                $handle3->bindValue(1, $delivery_date);
                $result3 = $handle3->execute();

                $value =explode("_",$arr[0]['Delivery_Slot']);
                $old_delivery_slot=$value[0].'_'.$value[1];
                $old_delivery_date = date('Y-m-d',strtotime($value[2]));

                $handle3 = $this->db->prepare("update time_slots set ".$old_delivery_slot."=".$old_delivery_slot."+1 where Slot_Date=?");
                $handle3->bindValue(1, $old_delivery_date);
                $result3 = $handle3->execute();

                $this->insertTransaction($_REQUEST['Customer_Mobno'],0,$_REQUEST['Order_Id'],static::$messages['Delivery_Slot_Updated']);
                $errresult['Message'] = $errresult['Message'].' '.static::$messages['Delivery_Slot_Updated'];
            }

            $errresult['Data'] = $data;
        }
        else {
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'].' '.static::$messages['Check_Order_Id'];;
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $entry_handle->errorCode();
        $this->db->commit();
        $this->db = null;
      
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
    }
}