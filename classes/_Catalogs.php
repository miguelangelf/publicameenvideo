<?php

class _Catalogs {
    
    public static function cities(){
        $sql = "SELECT id,name FROM delegations ORDER BY name";
        $pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($row = Database::fetch_array($pdo)){
            $resp[] = array("id"=>$row["id"],"name"=>$row["name"]);
        }
        return $resp;
    }
    
    public static function city($id){
        $sql  = "SELECT name FROM delegations WHERE id=$id";
        $pdo  = Database::executeConn($sql,"pilares");
        $resp = Database::fetch_row($pdo);
        return $resp["name"];
    }

    public static function studies(){
        $sql = "SELECT id,degree FROM degrees";
        $pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($row = Database::fetch_array($pdo)){
            $resp[] = array("id"=>$row["id"],"name"=>($row["degree"]));
        }
        return $resp;
    }
    
    public static function getMonth($id){
        switch($id){
            case 1: return "Enero";
            case 2: return "Febrero";
            case 3: return "Marzo";
            case 4: return "Abril";
            case 5: return "Mayo";
            case 6: return "Junio";
            case 7: return "Julio";
            case 8: return "Agosto";
            case 9: return "Septiembre";
            case 10: return "Octubre";
            case 11: return "Noviembre";
            case 12: return "Diciembre";
            default: "ERROR";
        }
    }
    
    public static function getDayOfWeek($id){
        switch($id){
            case 0: return "Domingo";
            case 1: return "Lunes";
            case 2: return "Martes";
            case 3: return "Miercoles";
            case 4: return "Jueves";
            case 5: return "Viernes";
            case 6: return "Sábado";
            default: "ERROR";
        }
    }
	
    public static function colonies( $cp = false){
        $sql = "
        SELECT C.id, C.name, D.name AS delegation_name, C.id_delegation 
        FROM colonies C
        INNER JOIN delegations D ON D.id = C.id_delegation	
        WHERE cp = '".$cp."' ";		
        $pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($row = Database::fetch_array($pdo)){
            $resp['colonies'][] = array("id"=>$row["id"],"name"=>$row["name"]);
            $resp['delegation'][0] = array('id'=>$row["id_delegation"], "name"=>$row["delegation_name"]);
        }
        return $resp;

    }
	
	
	public static function sectors(){
		
		$sql = "
		SELECT * 
		FROM sectors
		";
		
		$pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($row = Database::fetch_array($pdo)){
            $resp[$row['id']] = $row["name"];          
        }
        return $resp;
		
	}
	
	
	public static function subSector( $id_sector ){
		
		$sql = "
		SELECT *
		FROM subsectors 
		WHERE id_sector = ".$id_sector."
		";
		
		$pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($row = Database::fetch_array($pdo)){
            $resp[] = array("id"=>$row["id"],"name"=>$row["name"]);          
        }
        return $resp;
		
	}
	
	
	public static function etnias(){
		$sql = "
		SELECT * 
		FROM etnias
		";
		
		$pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($row = Database::fetch_array($pdo)){
            $resp[$row['id']] = $row["name"];          
        }
        return $resp;
	}

    public static function getMonthId($id){
        switch($id){
            case "Ene": return '01';
            case "Feb": return '02';
            case "Mar": return '03';
            case "Abr": return '04';
            case "May": return '05';
            case "Jun": return '06';
            case "Jul": return '07';
            case "Ago": return '08';
            case "Sep": return '09';
            case "Oct": return '10';
            case "Nov": return '11';
            case "Dic": return '12';
            default: "01";
        }
    }

    public static function pr( $data ){
        if(is_array($data) || is_object($data)){
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }else{
            $parce_data = json_decode($data);
            if(!empty($parce_data)){
                echo "<pre>";
                print_r($data);
                echo "</pre>";  
            }else{
                echo "<pre>";
                echo $data;
                echo "</pre>";              
            }
        }

    }
	
	public static function findCountries(){

		$records = array();
		$sql = "
		SELECT
			country_iso_code,
			country_name
		FROM countries C
		WHERE locale_code = 'es' 
		AND country_name != ''
		AND country_iso_code != ''
		ORDER BY country_name
		";
		$pdo = Database::executeConn($sql, 'geo');
		while($row = Database::fetch_array($pdo)){			
			$records[$row['country_iso_code']] = utf8_encode($row['country_name']);
		}

		return $records;

	}
	
}
