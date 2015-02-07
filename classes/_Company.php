<?php

class _Company {
    
    public static function getCompanyID($uid){
        $sql = "SELECT id_company FROM users_companies WHERE uid= $uid LIMIT 1";
        $pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            $row = Database::fetch_row($pdo);
            return $row["id_company"];
        }
        return null;
    }
	
	public static function allData( $id_company, $rows = '*' ){
		$sql = "
		SELECT 
		".$rows." 
		FROM companies
		WHERE id = ".$id_company."
		";
		$pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            return Database::fetch_row($pdo);
        }
        return false;
	}
}
