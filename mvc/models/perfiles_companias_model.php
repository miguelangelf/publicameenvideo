<?php

class perfiles_companias_model{
	    
    public function getActiveCompanies(){
        $sql = "SELECT `id`, `name` FROM companies WHERE `status` = 1";
        $pdo = Database::executeConn($sql, "pilares");
        $r = array();
        $cnt = 0;
        while($row = Database::fetch_array($pdo)){
            $r[$cnt]["id"] = $row['id'];
            $r[$cnt]["name"] = $row['name'];
            $cnt++;
        }
        return ($r);
    }
	
	
	 public function getCareerSitesCompanies(){
        $sql = "SELECT `id`, `name`, career_site FROM companies WHERE `status` = 1 AND career_site IS NOT NULL AND career_site != '' ";
        $pdo = Database::executeConn($sql, "pilares");
        $r = array();
        $cnt = 0;
        while($row = Database::fetch_array($pdo)){
            $r[$cnt]["id"] = $row['id'];
            $r[$cnt]["name"] = $row['name'];
			$r[$cnt]["career_site"] = $row['career_site'];
            $cnt++;
        }
        return ($r);
    }

    public function getCompanyProfile($id){
        $sql = "
                SELECT 
                    a.name as company, a.mission, a.website, a.description, a.company_size, a.subsidiaries, a.presence, a.street, a.outside_number, a.internal_number, a.zip_code, b.name as colonia, c.name as delegacion, a.career_site
                FROM 
                    companies a, colonies b, delegations c
                WHERE
                    a.id = '".$id."' AND a.status = 1 AND b.cp = a.zip_code AND b.id_delegation = c.id";
        
        $pdo = Database::executeConn($sql, "pilares");
        $r = array();
        $row = Database::fetch_assoc($pdo);
        return ($row);
    }
    
}
?>