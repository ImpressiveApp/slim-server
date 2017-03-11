<?php

namespace App\Controllers;

class RateCard extends Controller
{
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