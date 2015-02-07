<?php

class filters_model{
    
    public function filters(){
        $sql = "SELECT * FROM filtertypes";
        $pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($r = Database::fetch_array($pdo)){
            $resp[] = $r;
        }
        return $resp;
    }
    
}