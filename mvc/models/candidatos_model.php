<?php
class candidatos_model{
	
	const HIRED = 1;
	const DISCARDED = 2;
	const DISMISS = 3;
	const INTERVIEW = 4;
	const TEST = 5;
	const CANDIDATE_ACCEPTED = 3;
	const NO_HIRE = 6;
	
	function find( $uid = 0, $params = array() ){
        if(isset($params["search"])){
            $_sql_search = ($params["search"] == "")? "": " AND (C.name LIKE '%".$params["search"]."%' OR C.last_name LIKE '%".$params["search"]."%' OR C.folio LIKE '%".$params["search"]."%')";
        }else{
            $_sql_search = "";
        }
        
        $_tags = "";
        if(isset($params["filters"])){
            $struct = $this->getSQLFilters($params["filters"]);
            $_sql_filters = $struct["sql"];   
            $_tags = $struct["tags"];
        }else{
            $_sql_filters = "";
        }
                
                
		$candidates = array();
		$ids_users = array();				
		$sql = "
		SELECT SQL_CALC_FOUND_ROWS C.*, D.degree, W.times, M.status AS marital_status, N.number AS number_sons, J.type AS job_type, DE.name AS delegation, CO.name AS colony, A.name AS position, 
                CE.Perfil1 as administrativo, CE.Perfil2 as comercial, CE.Perfil3 as operativo, CE.Perfil4 as servicio,
                CE.Factor1 as f1,CE.Factor2 as f2,CE.Factor3 as f3,CE.Factor4 as f4,CE.Factor5 as f5,CE.Factor6 as f6,CE.Factor7 as f7,CE.Factor8 as f8,CE.Factor9 as f9,CE.Factor10 as f10,CE.Factor11 as f11,CE.Factor12 as f12,CE.Factor13 as f13,CE.Factor14 as f14,CE.Factor15 as f15,CE.Factor16 as f16, GE.name AS etnia, LW.name AS live_with, PE.proof AS proof_of_studies
                ". (!empty($params['id_company']) ? ", CCI.uid AS with_interview " : " ") ."
		FROM candidates C
		LEFT JOIN areas A ON A.id = C.id_area
		LEFT JOIN delegations DE ON C.id_delegation = DE.id
		LEFT JOIN colonies CO ON C.id_colony = CO.id
		LEFT JOIN degrees D ON D.id = C.id_degree
		LEFT JOIN working_times W ON W.id = C.id_working_time
		LEFT JOIN marital_status M ON M.id = C.id_marital_status
		LEFT JOIN number_sons N ON N.id = C.id_number_sons
		LEFT JOIN job_types J ON J.id = C.id_job_type
		LEFT JOIN candidate_evaluations CE ON C.folio = CE.folio
		LEFT JOIN etnias GE ON C.id_etnia = GE.id
		LEFT JOIN live_with LW ON C.id_live_with = LW.id
		LEFT JOIN proof_of_studies PE ON C.id_proof_of_studies = PE.id
		";
                
                
        if(!empty($uid)){
            $sql .= " WHERE C.uid = ".$uid." ";
        }else{
            if(!empty($params['id_company'])){
                $sql .= "
                LEFT JOIN candidate_company_status CCI ON C.uid = CCI.uid 
				AND CCI.id_status = ".self::INTERVIEW." 
				AND CCI.id_company = ".$params['id_company']." 
                LEFT JOIN candidate_company_status CC ON C.uid = CC.uid 
                AND (
                    CC.id_status = ".self::DISCARDED."
                    OR CC.id_status = ".self::NO_HIRE."
                )
                AND CC.id_company = ".$params['id_company']."
                WHERE C.hired = 0                 
                AND CC.id_status IS NULL 
                AND exam_finished = 1
                ".$_sql_search.$_sql_filters;
            }else{
                return false;
            }
        }

        $sql .= "
        GROUP BY C.uid  
        ORDER BY C.created DESC
        ";
		
                
		if(!empty($params['status']) && !empty($params['id_company'])){						
			
			switch($params['status']){
				case 'hired':
					$sql = "
					SELECT SQL_CALC_FOUND_ROWS C.*, D.degree, CC.created AS hired_date, (TO_DAYS(CURDATE())-TO_DAYS(CC.created)) as hired_days,
					(SELECT COUNT(*) 
					FROM candidate_company_status
					WHERE id_company = CC.id_company
					AND uid = CC.uid
					AND id_status = ".self::DISMISS."
					) AS dismiss,
					DE.name AS delegation, CO.name AS colony
					FROM candidates C
					LEFT JOIN delegations DE ON C.id_delegation = DE.id
					LEFT JOIN colonies CO ON C.id_colony = CO.id 
					INNER JOIN candidate_company_status CC ON C.uid = CC.uid
					AND CC.id_status = ".self::HIRED."		
					AND CC.id_company = ".$params['id_company']."			
					LEFT JOIN degrees D ON D.id = C.id_degree					
					HAVING dismiss = 0					 
					 ".$_sql_search;
				break;
				case 'interview':
					$sql = "
					SELECT SQL_CALC_FOUND_ROWS C.*, D.degree, DE.name AS delegation, CO.name AS colony,
					(SELECT COUNT(*) 
					FROM candidate_company_status
					WHERE id_company = CC.id_company
					AND uid = CC.uid
					AND (
							id_status = ".self::DISCARDED."
							OR id_status = ".self::TEST."
						)
					) AS discarded,
					CE.Perfil1 as administrativo, CE.Perfil2 as comercial, CE.Perfil3 as servicio, CE.Perfil4 as operativo,
                CE.Factor1 as f1,CE.Factor2 as f2,CE.Factor3 as f3,CE.Factor4 as f4,CE.Factor5 as f5,CE.Factor6 as f6,CE.Factor7 as f7,CE.Factor8 as f8,CE.Factor9 as f9,CE.Factor10 as f10,CE.Factor11 as f11,CE.Factor12 as f12,CE.Factor13 as f13,CE.Factor14 as f14,CE.Factor15 as f15,CE.Factor16 as f16
					FROM candidates C
					LEFT JOIN delegations DE ON C.id_delegation = DE.id
					LEFT JOIN colonies CO ON C.id_colony = CO.id
					INNER JOIN candidate_company_status CC ON C.uid = CC.uid
					AND CC.id_status = ".self::INTERVIEW."
					AND CC.id_company = ".$params['id_company']."					 
					LEFT JOIN degrees D ON D.id = C.id_degree
					LEFT JOIN candidate_evaluations CE ON C.folio = CE.folio
					WHERE C.hired = 0
					".$_sql_search."
					GROUP BY C.uid	
					HAVING discarded = 0				
					";
				break;
				case 'test':
					$sql = "
					SELECT SQL_CALC_FOUND_ROWS C.*, D.degree, DE.name AS delegation, CO.name AS colony, CC.created AS test_date, (TO_DAYS(CURDATE())-TO_DAYS(CC.created)) as test_days,
					(SELECT COUNT(*) 
					FROM candidate_company_status
					WHERE id_company = CC.id_company
					AND uid = CC.uid
					AND (
							id_status = ".self::DISCARDED."
							OR id_status = ".self::NO_HIRE."
						)
					) AS discarded
					FROM candidates C
					LEFT JOIN delegations DE ON C.id_delegation = DE.id
					LEFT JOIN colonies CO ON C.id_colony = CO.id
					LEFT JOIN degrees D ON D.id = C.id_degree
					INNER JOIN candidate_company_status CC ON C.uid = CC.uid
					AND CC.id_status = ".self::TEST."
					AND CC.id_company = ".$params['id_company']."
					WHERE C.hired = 0
					".$_sql_search."
					GROUP BY C.uid	
					HAVING discarded = 0
					";	
				break;
			}
		}
		
		if(!empty($params['page']) && !empty($params['rows']) && is_numeric($params['page']) && is_numeric($params['rows'])){
			$sql .=	" LIMIT ".(($params['page']-1)*$params['rows']).", ".$params['rows']." ";
		}
		
		//echo $sql;		
		$conn = Database::getExternalConnection("pilares");
                $pdo = $conn->query($sql);
		$pdo2 = $conn->query("SELECT FOUND_ROWS() AS candidates");					
		$count = Database::fetch_assoc($pdo2);
		unset($conn);		
		while($row = Database::fetch_array($pdo)){
			if(!empty($row['searches'])){
				$row['searches'] = explode(',', $row['searches']);
			}
			$candidates['candidate'][$row['uid']] = $row;
                        if(!empty($candidates['candidate'][$row['uid']]['birthdate'])){
                            $candidates['candidate'][$row['uid']]['age'] = floor((time() - strtotime($candidates['candidate'][$row['uid']]['birthdate']))/(60*60*24*365));
                        }else{
                            $candidates['candidate'][$row['uid']]['age'] = 0;
                        }
                        //$ids_users[]	 = $row['uid'];
		}				
		
		if(!empty($candidates)){			
			/*$usersData = $this->getUsersData( $ids_users );									
			foreach($usersData as $user){				
				$candidates['candidate'][$user['uid']] = array_merge($candidates['candidate'][$user['uid']], $user);
				$candidates['candidate'][$user['uid']]['age'] = floor((time() - strtotime($candidates['candidate'][$user['uid']]['birthdate']))/(60*60*24*365));									
			}*/						
			$candidates['total_rows'] = $count['candidates'];
            $candidates["tags"] = $_tags;
		}								
		
                
		return $candidates;
		
	}
	
	function getUsersData($ids_users){
		
		$users = array();
		
		$sql = "
		SELECT U.uid, U.name, U.last_name, U.email  
		FROM us_users U		
		WHERE U.uid IN (".implode(',', $ids_users).")
		";
		
		$pdo = Database::execute($sql);
		while($row = Database::fetch_array($pdo)){			
			$users[] = $row;
		}
		
		return $users;
		
	}

	function getDocumentsCompany( $data ){

		$files = array();
		$sy_file_id = array();
		$sy_files = array();
		
		$sql = "
		SELECT * 
		FROM documents_candidates_company DC		
		WHERE DC.id_candidate = ".$data['id_candidate']."
		AND DC.id_company = ".$data['id_company']."
		ORDER BY DC.id DESC 		
		";
		
		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_array($pdo)){			
			$files[] = $row;
 			$sy_file_id[] = $row['fid'];
		}
		
		if(!empty($sy_file_id)){
			
			$sql = "
			SELECT * 
			FROM sy_files
			WHERE fid IN(".implode(',', $sy_file_id).")
			";
			
			$pdo = Database::execute($sql);
			while($row = Database::fetch_array($pdo)){
				$sy_files[$row['fid']] = $row; 
			}
			
		}
		
		foreach($files as $key=>$file){
			if(!empty($file['fid']) && !empty($sy_files[$file['fid']])){
				$files[$key]['file'] = $sy_files[$file['fid']];
			}
		}
		
		return $files;

	}
	
	function getDocuments( $uid ){
		
		$files = array();
		$sy_file_id = array();
		$sy_files = array();
		
		$sql = "
		SELECT * 
		FROM documents_candidates DC
		INNER JOIN documents D ON DC.id_document = D.id
		WHERE DC.uid = ".$uid."		
		";
		
		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_array($pdo)){			
			$files[] = $row;
 			$sy_file_id[] = $row['sy_file_id'];
		}
		
		if(!empty($sy_file_id)){
			
			$sql = "
			SELECT * 
			FROM sy_files
			WHERE fid IN(".implode(',', $sy_file_id).")
			";
			
			$pdo = Database::execute($sql);
			while($row = Database::fetch_array($pdo)){
				$sy_files[$row['fid']] = $row; 
			}
			
		}
		
		foreach($files as $key=>$file){
			if(!empty($file['sy_file_id']) && !empty($sy_files[$file['sy_file_id']])){
				$files[$key]['file'] = $sy_files[$file['sy_file_id']];
			}
		}
		
		return $files;
		
	}
	
	function invite($candidate_id, $company_id, $date = NULL){
		
		$succes = 0;		
		$succes += $this->candidateCompanyStatus($candidate_id, $company_id, self::INTERVIEW) ? 0 : 1;
		
		
		$sql = "INSERT INTO `interviews_candidates`
		(
		`id_candidate`,
		`id_company`,
		`date_time`)
		VALUES(".$candidate_id.", ".$company_id.", '".$date."')";		
		$succes += (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;
		
		
		if($succes == 0){
			return true;
		}else{
			return false;	
		}						
		
	}
	
	function hire($candidates = array(), $company_id = NULL){
		$succes = 0;
		if(!empty($candidates) && !empty($company_id)){
			$sql = "
			UPDATE candidates
			SET hired = ".self::HIRED."
			WHERE uid IN(".implode(',', $candidates).")
			";			
			$succes += (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;				
			
			$succes += $this->candidateCompanyStatus($candidates, $company_id, self::HIRED) ? 0 : 1;
			
			if($succes == 0){
				return true;
			}else{
				return false;	
			}
		}else{
			return false;
		}		
	}
	
	function discard($candidates = array(), $company_id = NULL){		
		return $this->candidateCompanyStatus($candidates, $company_id, self::DISCARDED);
		
	}
	
	function dismiss($candidates = array(), $company_id = NULL, $dismiss = array()){
		$succes = 0;
		foreach($candidates as $candidate){			
			$sql = "
			INSERT INTO `dismiss_info`(
				`uid`, 
				`date`, 
				`id_dismiss_reason`, 
				`created`
			) VALUES (
				".$candidate.",
				'".date('Y-m-d', strtotime(str_replace('/', '-', $dismiss['date'])))."',
				".$dismiss['reason'].",
				NOW()
			)
			";			
			$pdo = Database::executeConn($sql, "pilares");
			if($pdo !== false){
				$succes += $this->candidateCompanyStatus($candidate, $company_id, self::DISMISS) ? 0 : 1;	
			}else{
				$succes ++;
			}
		}
		
		if($succes == 0){
			return true;
		}else{
			return false;	
		}				
	}
	
	
	private function candidateCompanyStatus($candidates = array(), $company_id = NULL, $status = ''){
		
		$candidates = !is_array($candidates) ? array($candidates) : $candidates;
		
		$succes = 0;						
		foreach($candidates as $candidate){
			$sql = "INSERT INTO candidate_company_status(uid, id_company, id_status, created) VALUES(".$candidate.", ".$company_id.", ".$status.", NOW())";
			$succes += (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;
		}
		if($succes == 0){
			return true;
		}else{
			return false;	
		}
		
	}
        
        public function getCities(){
            $sql = "SELECT id,name FROM delegations ORDER BY name";
            $pdo = Database::executeConn($sql,"pilares");
            $resp = array();
            while($row = Database::fetch_array($pdo)){
                $resp[] = array("id"=>$row["id"],"name"=>$row["name"]);
            }
            return $resp;
        }
        
        public function getStudies(){
            $sql = "SELECT id,degree FROM degrees";
            $pdo = Database::executeConn($sql,"pilares");
            $resp = array();
            while($row = Database::fetch_array($pdo)){
                $resp[] = array("id"=>$row["id"],"name"=>($row["degree"]));
            }
            return $resp;
        }
        
        private function getStudy($id){
            $sql = "SELECT id,degree FROM degrees WHERE id=".$id;
            $pdo = Database::executeConn($sql,"pilares");
            $row = Database::fetch_row($pdo);
            return $row["degree"];
        }
        
        private function getHijos($id){
            switch($id){
                case 1: return "Sin hijos";
                case 3: return "Hasta dos hijos";
                case 4: return "Mas de dos hijos";
            }
        }
        
        private function getEstado($id){
            switch($id){
                case 1: return "Casado(a)";
                case 2: return "Soltero(a)";
                case 3: return "Divorciado(a)";
                case 4: return "Unión libre";
            }
        }
        
        private function getTurno($id){
            switch($id){
                case 1: return "Matutino";
                case 2: return "Vespertino";
                case 3: return "Nocturno";
                case 4: return "Indistinto";
            }
        }
        
        private function ModuleFilters(){
            include_once Config::get("Core.Path.core")."/".module_engine::MODULE_DIRECTORY."/filters/filters.php";
            return new filters("","filters");
        }
        
        private function getSQLFilters($filter_params){        	
            $_sql_filters = "";  
            $first = true;
            $_tags = "";
            if(isset($filter_params) && !empty($filter_params)){
                $module = $this->ModuleFilters();
                $_f = $filter_params;
                if(isset($_f["edad_mayor"]["value"]) && $_f["edad_mayor"]["value"] != ""){
                    $obj = $module->sqlString($_f["edad_mayor"]["id"],array("v1"=>$_f["edad_mayor"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= $obj["tag"];
                }
                if(isset($_f["edad_menor"]["value"]) && $_f["edad_menor"]["value"] != ""){
                    $obj = $module->sqlString($_f["edad_menor"]["id"],array("v1"=>$_f["edad_menor"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= $obj["tag"];
                }
                if(isset($_f["genero"]["value"]) && $_f["genero"]["value"] != "-1"){
                    $obj = $module->sqlString($_f["genero"]["id"],array("v1"=>$_f["genero"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= $obj["tag"] == '1'? '<span class="label filter-tag tag-genero">Hombre <i class="fa fa-times close-tag" onclick="deltag(\'genero\')"></i></span>':'<span class="label filter-tag tag-genero">Mujer <i class="fa fa-times close-tag" onclick="deltag(\'genero\')"></i></span>';
                }
                
                // Delegacion
                if(isset($_f["delegacion"]["value"]) && $_f["delegacion"]["value"] != "0"){
                    $obj = $module->sqlString($_f["delegacion"]["id"],array("v1"=>$_f["delegacion"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= '<span class="label filter-tag tag-delegacion">'.  _Catalogs::city($obj["tag"]).' <i class="fa fa-times close-tag" onclick="deltag(\'delegacion\')"></i></span>';
                }
                if(isset($_f["licencia"]["value"]) && $_f["licencia"]["value"] != "-1"){
                    $obj = $module->sqlString($_f["licencia"]["id"],array("v1"=>$_f["licencia"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= $obj["tag"] == '1'? '<span class="label filter-tag tag-licencia">Con licencia <i class="fa fa-times close-tag" onclick="deltag(\'licencia\')"></i></span>':'<span class="label filter-tag tag-licencia">Sin licencia <i class="fa fa-times close-tag" onclick="deltag(\'licencia\')"></i></span>';
                }
                if(isset($_f["estudios"]["value"]) && $_f["estudios"]["value"] != "0"){
                    $obj = $module->sqlString($_f["estudios"]["id"],array("v1"=>$_f["estudios"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-estudios'>".$this->getStudy($obj["tag"])." <i class='fa fa-times close-tag' onclick='deltag(\"estudios\")'></i></span>";
                }
                if(isset($_f["concluidos"]["value"]) && $_f["concluidos"]["value"] != "-1"){                	
                    $obj = $module->sqlString($_f["concluidos"]["id"],array("v1"=>$_f["concluidos"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= $obj["tag"] == '1'? '<span class="label filter-tag tag-concluidos">Estudios concluidos <i class="fa fa-times close-tag" onclick="deltag(\'concluidos\')"></i></span>':'<span class="label filter-tag tag-concluidos">Estudios no concluidos <i class="fa fa-times close-tag" onclick="deltag(\'concluidos\')"></i></span>';
                }
                if(isset($_f["hijos"]["value"]) && $_f["hijos"]["value"] != "-1"){
                    $obj = $module->sqlString($_f["hijos"]["id"],array("v1"=>$_f["hijos"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-hijos'>".$this->getHijos($obj["tag"])." <i class='fa fa-times close-tag' onclick='deltag(\"hijos\")'></i></span>";
                }
                
                // Estado civil                
                if(isset($_f["estado"]["value"]) && $_f["estado"]["value"] != "0"){
                    $obj = $module->sqlString($_f["estado"]["id"],array("v1"=>$_f["estado"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-estado'>".$this->getEstado($obj["tag"])." <i class='fa fa-times close-tag' onclick='deltag(\"estado\")'></i></span>";
                }
                
                // Turno deseado
                if(isset($_f["turno"]["value"]) && $_f["turno"]["value"] != "0"){
                    $obj = $module->sqlString($_f["turno"]["id"],array("v1"=>$_f["turno"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-turno'>".$this->getTurno($obj["tag"])." <i class='fa fa-times close-tag' onclick='deltag(\"turno\")'></i></span>";
                }
                
                // Perfil administrativo
                if(isset($_f["perfil1"]["value"]) && $_f["perfil1"]["value"] == "1"){
                    $obj = $module->sqlString($_f["perfil1"]["id"],array("v1"=>$_f["perfil1"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-perfil1'>".$obj["tag"]." <i class='fa fa-times close-tag' onclick='deltag(\"perfil1\")'></i></span>";
                }
                
                // Perfil comercial
                if(isset($_f["perfil2"]["value"]) && $_f["perfil2"]["value"] == "1"){
                    $obj = $module->sqlString($_f["perfil2"]["id"],array("v1"=>$_f["perfil2"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-perfil2'>".$obj["tag"]." <i class='fa fa-times close-tag' onclick='deltag(\"perfil2\")'></i></span>";
                }
                
                // Perfil operativo
                if(isset($_f["perfil3"]["value"]) && $_f["perfil3"]["value"] == "1"){
                    $obj = $module->sqlString($_f["perfil3"]["id"],array("v1"=>$_f["perfil3"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-perfil3'>".$obj["tag"]." <i class='fa fa-times close-tag' onclick='deltag(\"perfil3\")'></i></span>";
                }
                
                // Perfil servicio al cliente
                if(isset($_f["perfil4"]["value"]) && $_f["perfil4"]["value"] == "1"){
                    $obj = $module->sqlString($_f["perfil4"]["id"],array("v1"=>$_f["perfil4"]["value"]));
                    if($first){
                        $_sql_filters .= $obj["sql"];
                        $first = false;
                    }else{
                        $_sql_filters .= " AND ".$obj["sql"];
                    }
                    $_tags .= "<span class='label filter-tag tag-perfil4'>".$obj["tag"]." <i class='fa fa-times close-tag' onclick='deltag(\"perfil4\")'></i></span>";
                }
                
            }
            $_sql_filters = "(".$_sql_filters.")";
            //echo $_sql_filters;
            
            if($_sql_filters == "()"){
                $_sql_filters = "";
            }else{
                $_sql_filters = " AND ".$_sql_filters;
            }
            return array("sql"=>$_sql_filters,"tags"=>$_tags);
        }
        
        public function getCandidate($uid){
            $sql = "SELECT C.*,D.name delegation FROM candidates C LEFT JOIN delegations D ON C.id_delegation=D.id WHERE uid=$uid LIMIT 1";
            $pdo = Database::executeConn($sql,"pilares");
            $num = Database::num_rows($pdo);
            if($num>0){
                return Database::fetch_row($pdo);
            }else{
                return null;
            }
        }
		
	function test($candidates = array(), $company_id = NULL){
		$succes = 0;
		
		$sql = "
		UPDATE candidates
		SET test = 1
		WHERE uid IN(".implode(',', $candidates).")
		";			
		$succes += (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;
		
		foreach($candidates as $candidate){			
			$succes += $this->candidateCompanyStatus($candidate, $company_id, self::TEST) ? 0 : 1;	
		}
		
		if($succes == 0){
			return true;
		}else{
			return false;	
		}				
	}
	
	
	function noHire($candidates = array(), $company_id = NULL){
		
		$succes = 0;
		
		$sql = "
		UPDATE candidates
		SET test = 0
		WHERE uid IN(".implode(',', $candidates).")
		";			
		$succes += (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;
		
		foreach($candidates as $candidate){		
			return $this->candidateCompanyStatus($candidates, $company_id, self::NO_HIRE);
		}
		
		if($succes == 0){
			return true;
		}else{
			return false;	
		}
	}


	function candidatesInTest(){		
		
		$candidates = array();
		
		$sql = "
		SELECT 
			C.uid,			
			C.name, 
			C.last_name,
			C.folio, 
			CCE.created,
			CCE.id_company, 
			C.email, 
			C.phone_number, 
			C.cell_number, 
			(30 - DATEDIFF(NOW(), CCE.created)) AS days,
			DATE_ADD(NOW(), INTERVAL (30 - DATEDIFF(NOW(), CCE.created)) DAY) AS last_date
		FROM candidate_company_status CCE
		INNER JOIN candidates C ON C.uid = CCE.uid 
		AND CCE.id_status = 5
		AND C.hired = 0
		AND DATEDIFF(NOW(), CCE.created) IN (25, 28)
		";
		
		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_array($pdo)){
			$companies[] = $row['id_company'];
			$candidates[] = $row;
		}
		
		return array('candidates' => $candidates, 'companies' => $companies);
			
	}


	function addNote( $data ){
		$sql ="
		INSERT INTO `candidate_user_notes`
		(
			`id_candidate`,
			`uid`,
			`note`,
			`created`
		)
		VALUES
		(			
			'".$data['id_candidate']."',
			'".$data['uid']."',
			'".$data['note']."',
			NOW()
		)
		";

		return Database::executeConn($sql, "pilares");
	}


	function getNotes( $data ){		
		
		$notes = array();
		
		$sql = "
		SELECT 
			C.note,
			C.created,
			U.name,
			U.last_name
		FROM candidate_user_notes C
		INNER JOIN users_companies U ON C.uid = U.uid 
		AND U.id_company = ".$data['id_company']."
		AND C.id_candidate = ".$data['id_candidate']."
		ORDER BY C.id DESC		
		";
		
		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_array($pdo)){			
			$notes[] = $row;
		}
		
		return array('notes' => $notes);
			
	}


	function getInterviewsDates( $data ){

		$interviews = array();
		
		$sql = "
		SELECT 
			DATE_FORMAT( date_time, '%d/%m/%Y %H:%i hrs') AS date_time
		FROM interviews_candidates C		
		WHERE C.id_company = ".$data['id_company']."
		AND C.id_candidate = ".$data['id_candidate']."
		ORDER BY C.date_time DESC		
		";
		
		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_array($pdo)){			
			$interviews[] = $row;
		}
		
		return array('interviews' => $interviews);

	}

	public function updateBackgroundProfile($user_id, $backposition, $type) {
		if($type == "avatar")
			$sql = "UPDATE candidates SET profile_picture = '".$backposition."' WHERE uid = $user_id";
		else
			$sql = "UPDATE candidates SET background_image = '".$backposition."' WHERE uid = $user_id";

		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		return $success;
	}

	public function updateLayout($uid, $view = null) {
		
		$sql = "UPDATE candidates SET view = $view WHERE uid = $uid";
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		return $success;
	}

	public function findCityState($id) {
		$records = array();
		if(empty($id)) return null;
		$sql = "
		SELECT
			subdivision_1_name,
			city_name
		FROM cities
		WHERE geoname_id = $id AND locale_code = 'es'";

		$pdo = Database::executeConn($sql, 'geo');
		while($row = Database::fetch_array($pdo)){	
			$records[] = array( 'state' => utf8_encode($row['subdivision_1_name']), 'city' => utf8_encode($row["city_name"]));
		}

		return $records;
	}
	
}
?>