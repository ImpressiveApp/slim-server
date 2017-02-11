<?php

namespace App\Controllers;

class Promocodes extends Controller
{


    public function getPromoCodes($request, $response)
    {
       // $this->db->beginTransaction();
        $sql = 'select * from promocodes';

        $handle = $this->db->prepare($sql);
        $result = $handle->execute();
        $data = $handle->fetchAll();
        $dataSend['Promolist'] = $data;
        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;

        if($data) {
            $errresult['Message'] = static::$messages['Data_true'];
            $errresult['Data'] = $dataSend;
        }
        else {
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
    public function createPromocode($request, $response)
	{
        $args = $request->getParsedBody();
        
        $this->db->beginTransaction();

        $handle = $this->db->prepare('insert into promocodes (Name, Criteria , Discount, Message,Valid,Valid_from,Valid_to) values(?,?,?,?,?,?,?)');

    	$_REQUEST['Name']=$args['name'];
        $_REQUEST['Criteria']=$args['criteria'];
        $_REQUEST['Discount']=$args['discount'];
        $_REQUEST['Message']=$args['message'];
        $_REQUEST['Valid']=$args['valid'];
        $_REQUEST['Valid_from']=$args['validfrom'];
        $_REQUEST['Valid_to']=$args['validto'];

    	$date = strtotime($_REQUEST['Valid_from']);
        $from_date = date('Y-m-d',$date);
        
        $date = strtotime($_REQUEST['Valid_to']);
        $to_date = date('Y-m-d',$date);

        $today = date("Y-m-d");

        if($from_date<=$today && $today<=$to_date ) 
            $_REQUEST['Valid']='Y';        
        else {
            $_REQUEST['Valid']='N';
            $this->logger->debug($from_date." is ahead of today ". $today);
        }


    	$handle->bindValue(1, $_REQUEST['Name']);
        $handle->bindValue(2, $_REQUEST['Criteria']);
        $handle->bindValue(3, $_REQUEST['Discount']);
        $handle->bindValue(4, $_REQUEST['Message']);
        $handle->bindValue(5, $_REQUEST['Valid']);  
        $handle->bindValue(6, $from_date);
        $handle->bindValue(7, $to_date);
     
        $result = $handle->execute();
        $id = $this->db->lastInsertId();

       
    
        //if($_REQUEST['Valid']=='Y' && $from_date<=$today && $today<=$to_date ) {
        if($_REQUEST['Valid']=='Y') {


            if($_REQUEST['Criteria']=='All') {

                $handle2 = $this->db->prepare('update customer_details set Applicable_Promocodes= concat(IFNULL(Applicable_Promocodes,?), ? )');
                   
                $handle2->bindValue(1, "");
                $handle2->bindValue(2, " ".$_REQUEST['Name']);
                $result2 = $handle2->execute();
             //   $this->logger->debug($handle2->rowCount().static::$messages['Customers_Applicable_Promocode'].$_REQUEST['Name']);
            }
		
            else {
        

                $value =explode("_",$_REQUEST['Criteria']);

                if($value[0]=='Order') {
                    $total_order=$value[1];
                
                    if($total_order==0) {
                        $handle2 = $this->db->prepare('update customer_details set Applicable_Promocodes= concat(IFNULL(Applicable_Promocodes,?), ? )
                                where Id IN (
                                    select temp.id from 
                                    (
                                        select c.id ,count(o.Order_Id)
                                        from customer_details c
                                        left join order_details o on c.Customer_Mobno=o.Customer_Mobno
                                        group by c.Customer_Mobno
                                        HAVING count(o.Order_Id) = ?
                                    ) temp 
                                ) and 
                                Id in 
                                (   select temp1.Id 
                                    from 
                                    (   select Id,Customer_Name, Used_Promocodes 
                                        from customer_details 
                                        where Used_Promocodes is null 
                                        union 
                                        select id, Customer_Name, Used_Promocodes 
                                        from customer_details 
                                        where Used_Promocodes not like ?
                                    ) temp1
                                )
                                        ');
                        
                    }
                   
                    else if($total_order>0) {
                        $handle2 = $this->db->prepare('update customer_details set Applicable_Promocodes= concat(IFNULL(Applicable_Promocodes,?), ? )
                                where Id IN (
                                    select temp.id from 
                                    (
                                        select c.id ,count(o.Order_Id)
                                        from customer_details c
                                        left join order_details o on c.Customer_Mobno=o.Customer_Mobno
                                        group by c.Customer_Mobno
                                        HAVING count(o.Order_Id) >= ?
                                    ) temp 
                                ) and 
                        Id in 
                (   select temp1.Id 
                    from 
                    (   select Id,Customer_Name, Used_Promocodes 
                        from customer_details 
                        where Used_Promocodes is null 
                        union 
                        select id, Customer_Name, Used_Promocodes 
                        from customer_details 
                        where Used_Promocodes not like ?
                    ) temp1
                )
                        ');

                  
                    }
                   
                    $handle2->bindValue(1, "");
                    $handle2->bindValue(2, " ".$_REQUEST['Name']);
                    $handle2->bindValue(3, $total_order);
                    $handle2->bindValue(4, '%'.$_REQUEST['Name'].'%');
                    $result2 = $handle2->execute();
              //      $this->logger->debug($handle2->rowCount().static::$messages['Customers_Applicable_Promocode'].$_REQUEST['Name']);

                }
           

                else if($value[0]=='Amount') {       
                    $total_amount=$value[1];
                    $handle2 = $this->db->prepare('update customer_details set Applicable_Promocodes= concat(IFNULL(Applicable_Promocodes,?), ?)
                            where Id IN (
                                select temp.id from
                                (                                                    select c.id ,c.Wallet
                                    from customer_details c
                                    left join order_details o on c.Customer_Mobno=o.Customer_Mobno
                                    group by c.Customer_Mobno
                                    having IFNULL(( c.Wallet+sum(o.Cost) ),0) >= ? 
                                ) temp 
                    ) and 
                                Id in 
                                (   select temp1.Id 
                                    from 
                                    (   select Id,Customer_Name, Used_Promocodes 
                                        from customer_details 
                                        where Used_Promocodes is null 
                                        union 
                                        select id, Customer_Name, Used_Promocodes 
                                        from customer_details 
                                        where Used_Promocodes not like ?
                                    ) temp1
                                )' );
   
                    $handle2->bindValue(1, "");
                    $handle2->bindValue(2, " ".$_REQUEST['Name']);
                    $handle2->bindValue(3, $total_amount);
                    $handle2->bindValue(4, '%'.$_REQUEST['Name'].'%');
                    $result2 = $handle2->execute();
                //    $this->logger->debug($handle2->rowCount().static::$messages['Customers_Applicable_Promocode'].$_REQUEST['Name']);

                }
            }
            $this->logger->debug($handle2->rowCount().static::$messages['Customers_Applicable_Promocode'].$_REQUEST['Name']);


    	}
    
        $handle = $this->db->prepare('Select * from promocodes where Id =?');
        $handle->bindValue(1, $id);
        $handle->execute();
   		$data = $handle->fetchObject();

        $errresult['Resultcode'] = static::$messages['Resultcode_0'];;
        $errresult['Message'] = static::$messages['Data_true'].' '.static::$messages['Promocode_Created'];
        $errresult['Data'] = $data;
        $errresult['StatusCode'] = $handle->errorCode();
        $this->db->commit();
        $this->db = null;

        //return $response->withJson($errresult);
       return $response
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errresult,JSON_PRETTY_PRINT));
	}

}
