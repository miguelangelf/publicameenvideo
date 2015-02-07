<?php

class _Offices {
    
    public static function getOfficeID($uid){
        $sql = "SELECT id_office FROM users_offices WHERE uid = $uid LIMIT 1";
        $pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            $row = Database::fetch_row($pdo);
            return $row["id_office"];
        }
        return null;
    }
    
    public static function getOfficeType($officeID){
        $sql = "SELECT type FROM offices WHERE id = $officeID LIMIT 1";
        $pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            $row = Database::fetch_row($pdo);
            return $row["type"];
        }
        return null;
    }
    
    public static function getModuleByCP($cp){
        $sql = "SELECT O.*,D.name delegation,C.name colony FROM offices O, offices_cps OCP,delegations D, colonies C "
                ." WHERE D.id =O.id_delegation AND C.id=O.id_colony AND OCP.cp = '$cp' AND OCP.office_id = O.id LIMIT 1";
        $pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            $row = Database::fetch_row($pdo);
            return $row;
        }
        return null;
    }
    
    public static function getModule($officeID){
        $sql = "SELECT O.*,D.name delegation,C.name colony FROM offices O,delegations D, colonies C WHERE D.id =O.id_delegation AND C.id=O.id_colony AND O.id = $officeID LIMIT 1";
        $pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            $row = Database::fetch_row($pdo);
            return $row;
        }
        return null;
    }
	
	
	public static function getOfficeAccessCode($uid){
        $sql = "
		SELECT access_code 
		FROM users_offices U
		INNER JOIN offices O ON O.id = U.id_office
		WHERE U.uid = $uid 
		LIMIT 1
		";
        $pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            $row = Database::fetch_row($pdo);
            return $row["access_code"];
        }
        return null;
    }
	
	
	public static function getOfficeData($officeID){
        $sql = "SELECT * FROM offices WHERE id = $officeID LIMIT 1";
        $pdo = Database::executeConn($sql,"pilares");
        $n = Database::num_rows($pdo);
        if($n>0){
            $row = Database::fetch_row($pdo);
            return $row;
        }
        return null;
    }
	
}
