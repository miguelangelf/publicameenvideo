<?php

class admin_model{

    public function users(){
        $sql = "SELECT name,last_name,email FROM us_users LIMIT 20";
        $pdo = Database::executeConn($sql,"default");
        $results = array();
        while($row = Database::fetch_array($pdo)){
            $results[] = $row;
        }
        return $results;
    }
    
}

?>
