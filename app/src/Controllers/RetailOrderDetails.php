<?php

namespace App\Controllers;

class RetailOrderDetails extends Controller
{
	public function getRetailOrders($request, $response)
	{
		$this->db->beginTransaction();

        $sql = 'Select * from retail_order_details where Order_status in (?)';

        $handle = $this->db->prepare($sql);
 	    $handle->bindValue(1, static::$messages['Open']);
   
  	   	$result = $handle->execute();
		$data = $handle->fetchAll();
        $dataSend['Order_Grid'] = $data;
		
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

	 public function createRetailOrder($request, $response)
    {
        $args = $request->getParsedBody();

        $this->db->beginTransaction();
 
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Customer_Name']=$args['name'];
        $_REQUEST['No_Of_Items']=$args['noOfItems'];
        $_REQUEST['Types']=$args['type'];
        $_REQUEST['Order_Status']=static::$messages['Open'];
        

        $handle = $this->db->prepare('insert into retail_order_details(Customer_Mobno,Customer_Name,Order_Status,No_Of_Items,Type_Of_Clothes) values(?,?,?,?,?)');
 
        $handle->bindValue(1, $_REQUEST['Customer_Mobno']);
        $handle->bindValue(2, $_REQUEST['Customer_Name']);
        $handle->bindValue(3, $_REQUEST['Order_Status']);
        $handle->bindValue(4, $_REQUEST['No_Of_Items']);
        $handle->bindValue(5, $_REQUEST['Types']);
        // Creating Order
        $result = $handle->execute();
        $order_id = $this->db->lastInsertId();
  
    //    $comment= static::$messages['Retail_Order_Created'].' '.static::$messages['Order_Id'].$order_id.'.';
      
        $handle = $this->db->prepare('Select * from retail_order_details where order_id=?');
        $handle->bindValue(1, $order_id);
        $handle->execute();
        $data = $handle->fetchObject();
            
        $errresult['Message'] = static::$messages['Data_true'].' '.static::$messages['Retail_Order_Created'].' '.static::$messages['Order_Id'].$order_id.'.'.' '.static::$messages['Show_SMS'];
        $errresult['Data'] = $data;
             
        $errresult['StatusCode'] = $handle->errorCode();
  
        $this->db->commit();
        $this->db = null;
      
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
    }
    
    public function updateRetailOrder($request, $response)
    {
        $args = $request->getParsedBody();

        $this->db->beginTransaction();

        $_REQUEST['Order_Id']=$args['orderid'];
        $_REQUEST['Customer_Mobno']=$args['mobno'];
        $_REQUEST['Order_Status']=$args['status'];
 		$_REQUEST['Cost']=$args['cost'];
     
        $entry_handle = $this->db->prepare('Select * from retail_order_details where order_id=? and Customer_Mobno=?');
        $entry_handle->bindValue(1, $_REQUEST['Order_Id']);
        $entry_handle->bindValue(2, $_REQUEST['Customer_Mobno']);
    
        $entry_result = $entry_handle->execute();
        
        $data = $entry_handle->fetchAll();

        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

     //   $sms_type=null;
        if($data) {

            $errresult['Message'] = static::$messages['Data_true'];

            $arr = array_values($data);
        
            if($_REQUEST['Order_Status'] != null) {
            	$order_handle = $this->db->prepare('update retail_order_details set Order_Status=? where order_id=?');

  				$order_handle->bindValue(1, $_REQUEST['Order_Status']);
          		$order_handle->bindValue(2, $_REQUEST['Order_Id']);
              	$result = $order_handle->execute();
            }
         
    
            if($_REQUEST['Cost'] >= 0) {
            	$order_handle = $this->db->prepare('update retail_order_details set Cost =? where order_id=?');

            	$order_handle->bindValue(1, $_REQUEST['Cost']);
              
              	$order_handle->bindValue(2, $_REQUEST['Order_Id']);
             	$result = $order_handle->execute();
            }

         /*
    $cols = array();

    foreach($data as $key=>$val) {
        $cols[] = "$key = '$val'";
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";*/
        /*
          $query = "UPDATE product SET";
$comma = " ";
$whitelist = array(
    'title',
    'rating',
    'season',
    'brand_id',
    'cateogry',
    // ...etc
);
foreach($_POST as $key => $val) {
    if( ! empty($val) && in_array($key, $whitelist)) {
        $query .= $comma . $key . " = '" . mysql_real_escape_string(trim($val)) . "'";
        $comma = ", ";
    }
}
$sql = mysql_query($query);*/

            $entry_handle->execute();
  			$data = $entry_handle->fetchObject();

            $errresult['Data'] = $data;
        }
        else {
            $errresult['Resultcode'] = static::$messages['Resultcode_1'];
            $errresult['Message'] = static::$messages['Data_false'].' '.static::$messages['Check_Mobile'].' '.static::$messages['Check_Order_Id'];;
            $errresult['Data'] = static::$messages['No_Data'];
        }
      
        $errresult['StatusCode'] = $entry_handle->errorCode();

     /*   $sms_number=$data->Customer_Mobno;
        $sms_data=array($data->Order_Id,$data->Cost,$data->Cost);
        if($sms_type!=null)$this->testsms($sms_number,$sms_type,$sms_data);
*/ 
        $this->db->commit();
        $this->db = null;
     
        //return $response->withJson($errresult);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
    
    }
}