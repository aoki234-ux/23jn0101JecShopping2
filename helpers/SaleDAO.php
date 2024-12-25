<?php
require_once 'DAO.php';
require_once 'CartDAO.php';
require_once 'SaleDetailDAO.php';

class SaleDAO { 
    public function insert(int $memberid,Array $cart_list) {

        $ret = false;

        $dbh = DAO::get_db_connect();

        try{
            $dbh->beginTransaction();

            $sql = "select * from Sale WITH(TABLOCK,HOLDLOCK)";
            $dbh->query($sql);

            $sql = "INSERT INTO sale(saledate,memberid) values(:saledate,:memberid)";

            $saledate = date("Y-m-d H:i:s");
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':saledate', $saledate, PDO::PARAM_STR);
            $stmt->bindValue(':memberid', $memberid, PDO::PARAM_INT);
            $stmt->execute();

            $saleno = $this->get_saleno();
            $saleDetailDAO = new SaleDetailDAO();
    
            foreach($cart_list as $cart){
                $saleDetail = new SaleDetail();
    
                $saleDetail->saleno=$saleno;
                $saleDetail->goodscode=$cart->goodscode;
                $saleDetail->num=$cart->num;
    
                $saleDetailDAO->insert($saleDetail,$dbh);
            }

            $dbh->commit();
                $ret = true;
        }catch(PDOExcepyion $e){
            
            $dbh->rollBack();
            $ret = false;
        }
        return $ret;
    }

    public function get_saleno(){
        $dbh = DAO::get_db_connect();

        $sql = "select IDENT_CURRENT('Sale') AS saleno";

        $stmt = $dbh->query($sql);

        $row = $stmt->fetchObject();
        return $row->saleno;
    }
}

?>