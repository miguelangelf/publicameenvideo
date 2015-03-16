<?php

class admin_model{

    public function users($actual,$message){
        $aux=$message;
        $sql = "SELECT distinct name,last_name,email FROM us_users where name like '%".$aux."%' or email like '%".$aux."%' or last_name like '%".$aux."%'   LIMIT ".($actual*20)." ,20";
        $pdo = Database::executeConn($sql,"default");
        $results = array();
        while($row = Database::fetch_array($pdo)){
            $results[] = $row;
        }
        return $results;
    }
    
    
    
    public function empresas(){
        $sql = "SELECT name,email FROM us_users LIMIT 20";
        $pdo = Database::executeConn($sql,"default");
        $results = array();
        while($row = Database::fetch_array($pdo)){
            $results[] = $row;
        }
        return $results;
    }
    
    
    public function videos(){
        $sql = "SELECT email FROM us_users LIMIT 20";
        $pdo = Database::executeConn($sql,"default");
        $results = array();
        while($row = Database::fetch_array($pdo)){
            $results[] = $row;
        }
        return $results;
    }
    
    

}

?>
