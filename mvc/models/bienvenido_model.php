<?php

class bienvenido_model{
	
	function find( $params ){
		
		$_sql_search = "";
		$data = array();
		if(!empty($params["search"])){
			$data = $this->searchBy($params["search"]);
			$_sql_search = $data['conditions'];            
        }
		
		$_sql_applied = !empty($params["uid"])  ? "
		LEFT JOIN vacancy_candidates V ON IFNULL(J.hash, J.id_source) = V.id_vacancy 
		AND V.id_candidate = ".$params["uid"]." 
		AND IF(J.hash IS NULL, 0, 1) = V.external
		" : "";
		
		$sql = "
		SELECT
			SQL_CALC_FOUND_ROWS  
			id,
			job_description,
			position,
			J.created AS modified,
			city AS city_name,
			state AS subdivision_1_name,
			country AS country_name,
			logo,
			company,
			hash,
			id_source
			".( !empty($params["uid"]) ? ", V.created AS applied" : "" )."
			".( !empty($data['match']) ? ", ".$data['match'] : "" )." 			
		FROM bash_stable_jobs J
		".$_sql_applied."
		WHERE 1
		".$_sql_search."
		GROUP BY J.id				
		".( !empty($data['match']) ? "ORDER BY keywords_search_rate DESC, location_search_rate DESC,  J.created DESC" : "ORDER BY rate DESC, J.created DESC" )."
		";
		
		if(!empty($params['page']) && !empty($params['rows']) && is_numeric($params['page']) && is_numeric($params['rows'])){
			$sql .=	"LIMIT ".(($params['page']-1)*$params['rows']).", ".$params['rows']." ";
		}
		//_Catalogs::pr($sql);		
		$conn = Database::getExternalConnection("pilares");
		$pdo = $conn->query($sql);
		$pdo2 = $conn->query("SELECT FOUND_ROWS() AS total_rows");					
		$count = Database::fetch_assoc($pdo2);
		unset($conn);
		$data['total_rows'] = $count['total_rows'];		
		while($row = Database::fetch_array($pdo)){
			$row['job_description'] = strip_tags($row['job_description']);
			$row['job_description'] = (substr($row['job_description'], 0, 310)).( strlen($row['job_description']) > 310 ? ' ... <span class="show-more">[ver más]</span>' : '' );			
			$row['position'] = ucfirst(mb_strtolower($row['position'], 'UTF-8'));			
			$row['company'] = empty($row['company']) ? 'Empresa' : $row['company'];
			$row['id'] = empty($row['hash']) ? $row['id_source'] : $row['hash'];
			!empty($row['applied']) ? $row['applied'] = array('created' => $row['applied']) : false;
			$row['city_name'] = mb_detect_encoding($row['city_name']) != 'UTF-8' ? $row['city_name'] : utf8_encode($row['city_name']);
			$row['subdivision_1_name'] = mb_detect_encoding($row['subdivision_1_name']) != 'UTF-8' ? $row['subdivision_1_name'] : utf8_encode($row['subdivision_1_name']);
			$row['country_name'] = mb_detect_encoding($row['country_name']) != 'UTF-8' ? $row['country_name'] : $row['country_name'];
			if(isset($row['location_search_rate'])){
				if($row['location_search_rate'] > 0 && $row['keywords_search_rate'] > 0 ){
					$row['search_rate'] = 'searched-vacancy-top';
				}
				if($row['location_search_rate'] == 0 && $row['keywords_search_rate'] > 0 ){
					$row['search_rate'] = 'searched-vacancy-keywords';
				}
				if($row['location_search_rate'] > 0 && $row['keywords_search_rate'] == 0 ){
					$row['search_rate'] = 'searched-vacancy-location';
				}
			}
			$data['rows'][] = $row;			
		}
        return $data;
	}
        
        function grouping($params){
            $_sql_search = "";
            if(!empty($params["search"])){
                $searchBy = $this->searchBy($params["search"]);
				$_sql_search = $searchBy['conditions']; 
            }
            $_sql_search = str_replace("'","\'",$_sql_search);
            $sql = "CALL get_grouping('".$_sql_search."')";
            $conn = Database::getExternalConnection("pilares");
            $pdo = $conn->query($sql);
            $data = array();
            while($row = Database::fetch_assoc($pdo)){
                foreach(explode("#", $row["json"]) as $element){
					$array = split(':', $element);
                    if(count($array) == 2){
						list($label, $value) = split(':', $element);
						$data[$row["attribute"]][utf8_encode($label)] = $value;
					}                    
                }
            }
            return json_encode($data);
        }
	
	function findById( $id_vacancy, $uid = false ){
		
		$_sql_applied = !empty($uid)  ? "LEFT JOIN vacancy_candidates V ON IFNULL(J.hash, J.id_source) = V.id_vacancy AND V.id_candidate = ".$uid." AND IF(J.hash IS NULL, 0, 1) = V.external" : "";
		
		$sql = "
		SELECT 
			J.*,
			city AS city_name,
			state AS subdivision_1_name,
			country AS country_name,
			address AS street,
			gender AS sex
			".( !empty($uid) ? ", V.created AS applied" : "" )." 
		FROM bash_stable_jobs J
		".$_sql_applied."
		WHERE IFNULL(J.hash, J.id_source) = ".$id_vacancy."				
		";
		
		$pdo = Database::executeConn($sql, 'pilares');		
		if($pdo !== false){
			$row = Database::fetch_row($pdo);
			$row['industry'] = mb_detect_encoding($row['industry']) != 'UTF-8' ? $row['industry'] : utf8_encode($row['industry']);
                        $row['function'] = mb_detect_encoding($row['function']) != 'UTF-8' ? $row['function'] : utf8_encode($row['function']);
			!empty($row['industry']) ? $row['industries'][0]['value'] = $row['industry'] : false;
			!empty($row['function']) ? $row['functions'][0]['value'] = $row['function'] : false;
			$row['position'] = ucfirst(mb_strtolower($row['position'], 'UTF-8'));
			$row['street'] = mb_detect_encoding($row['street']) != 'UTF-8' ? $row['street'] : utf8_encode($row['street']);
			$row['city_name'] = mb_detect_encoding($row['city_name']) != 'UTF-8' ? $row['city_name'] : utf8_encode($row['city_name']);
			$row['subdivision_1_name'] = mb_detect_encoding($row['subdivision_1_name']) != 'UTF-8' ? $row['subdivision_1_name'] : utf8_encode($row['subdivision_1_name']);
			$row['country_name'] = mb_detect_encoding($row['country_name']) != 'UTF-8' ? $row['country_name'] : $row['country_name'];
			if(!empty($row['file'])){
				$data = json_decode($row['file']);
				if(!empty($data->fid)){
					$sql = "
					SELECT * 
					FROM sy_files
					WHERE fid = ".$data->fid."
					";
					

					$pdo = Database::execute($sql);
					$row['file'] = Database::fetch_row($pdo);
				}
				!empty($data->attachment_name) ? $row['attachment_name'] = $data->attachment_name : false;
			}
			!empty($row['applied']) ? $row['applied'] = array('created' => $row['applied']) : false;
			$row['external'] = empty($row['hash']) ? 0 : 1;
			return $row;
		}else{
			return false;
		}
	}
	
	
	function topVacancies(){
		$records = array();
		
		$sql = "
		SELECT 
			position,
			job_description,
			hash,
			id_source,
			created,
			city,
			state,
			country
		FROM bash_stable_jobs 				
		ORDER BY rate DESC, created DESC		
		LIMIT 9
		";
		
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){
			$row['job_description'] = trim(mb_strtolower(strip_tags($row['job_description']), 'UTF-8'));
			$row['job_description'] = (substr($row['job_description'], 0, 60)). ' ... <span class="show-more">[ver más]</span>';			
			$row['position'] = ucfirst(mb_strtolower($row['position'], 'UTF-8'));
			$row['position_full'] = $row['position'];
			$row['position'] = (substr($row['position'], 0, 35)).( strlen($row['position']) > 35 ? ' ...' : '' );			
			$row['id'] = empty($row['hash']) ? $row['id_source'] : $row['hash'];
			$row['city'] = utf8_encode($row['city']);
			$row['state'] = utf8_encode($row['state']);
			$row['country'] = utf8_encode($row['country']);			
			$records[] = $row;
		}
		
		return $records;
	}
	
	
	function applyData( $token, $id_vacancy ){
		
		$sql = "
		SELECT 
			*
		FROM email_applicants
		WHERE status = 1
		AND token = '".$token."'	
		AND id_vacancy = ".$id_vacancy."	
		";
		
		$pdo = Database::executeConn($sql, 'pilares');
		if($pdo !== false){
			return Database::fetch_row($pdo);
		}else{
			return false;
		}		
	}
	
	public function getCandidateByUid( $uid ){
		
		$sql = "
		SELECT * 
		FROM us_users U
		INNER JOIN us_users_passwords P ON U.uid = P.uid
		WHERE U.uid = ".$uid."
		";
		
		$pdo = Database::execute($sql);
		if($pdo !== false){
			return Database::fetch_row($pdo);
		}
		
	}	
	
	
	function applyDataUpdate( $token, $id_vacancy ){
		
		$sql = "
		UPDATE email_applicants
		SET status = 0,
		modified = NOW()
		WHERE token = '".$token."'	
		AND id_vacancy = ".$id_vacancy."	
		";
		
		return Database::executeConn($sql, 'pilares');				
	}
	
	
	function tokenToVacancyApply($uid, $id_vacancy){
		$vacancy = $this->findById($id_vacancy);
		$response['data'] = array();	
		if(!empty($vacancy)){
			$token = md5(uniqid(mt_rand(), true));;
			$sql = "
			INSERT INTO `email_applicants`(
				`id_candidate`,
				`id_vacancy`,
				`external`,
				`status`,
				`token`,
				`created`,
				`modified`
			) VALUES(
				".$uid.",
				".$id_vacancy.",
				".$vacancy['external'].",
				1,
				'".$token."',
				NOW(),
				NOW()
			)";
			
			$response['pdo'] = Database::executeConn($sql, 'pilares');
			$response['data'] = $vacancy;
			$response['data']['position'] = urlencode($response['data']['position']);
			$response['data']['aplicant'] = $token;
			$response['data']['id'] = $id_vacancy;
			
			
		}
		
		return $response;
	}	
	
	function searchBy( $string ){
		
		$simple = array();
		$searchBy = array();
		$data = array();
		$sql = "";
		$searchIn = array(
			'position',
			'job_description',
			'city',
			'state',
			'company',
			'address',			
			'position',
			'function',
			'industry',
			'email',
			'salary'		
		);
		
		$rules = array(
			'\+[^("|\s)]+\s',
			'-[^("|\s)]+\s',
			'"[^("|\s)]+"',
			'\+"[^"]+"',
			'-"[^"]+"',
			'[^("|\s)]+\s'
			
		);
		preg_match_all('/(('.implode('|', $searchIn).')(:|:\s)('.implode('|', $rules).'))/', $string, $results);		
		if(!empty($results[1])){
			$searchBy = $results[1];
			$sql .= $this->parseFor($searchBy, $searchIn);
			$string = str_replace($results[0], '', $string);
		}		
		$string = trim($string);
		//_Catalogs::pr($string);	
		//_Catalogs::pr($searchBy);	
		if(!empty($string)){
			$match = $this->appendMatch($string);
			$sql .= $match['conditions'];
			$data['match'] = $match['fields'];
		}
		
		$data['conditions'] = $sql;
					
		return $data;
	}
	
	
	function appendAnd($text, $fields, $type = true){
		$sql = $type ?  "AND ( " : "AND NOT ( ";		
		$text = is_array($text) ? $text : array($text);		
		$items = count($fields)*count($text) - 1;
		$key = 0;
		foreach($fields as $num => $field){
			foreach($text as $value){
				$sql .= "
				".$field." LIKE '%".$value."%' ";
				if( $key < $items){
					$sql .=  "OR";
				}
				$key ++;
			}
		}
		$sql .= "
		) 
		";
		return $sql;		
	}
	
	function appendMatch( $text ){
		$data = array();
		/*
		$data['conditions'] = "
		AND IF( MATCH(location_search) AGAINST ('".$text."' IN BOOLEAN MODE) > 0 , 1 , MATCH(keywords_search) AGAINST ('".$text."' IN BOOLEAN MODE) ) != 0 
		";
		*/
				
		$data['conditions'] = "		
		AND MATCH(location_search) AGAINST ('".$text."' IN BOOLEAN MODE)
		OR MATCH(keywords_search) AGAINST ('".$text."' IN BOOLEAN MODE)
		";		
		
		$data['fields'] = "
		MATCH(location_search) AGAINST ('".$text."' IN BOOLEAN MODE) AS location_search_rate,		
        MATCH(keywords_search) AGAINST ('".$text."' IN BOOLEAN MODE) AS keywords_search_rate
		";
		
		return $data;
		
	}
	
	function parseFor( $array_terms, $searchIn ){
		$searchBy = array();
		$sql = "";
		$string = implode(' ', $array_terms);
		$string = str_replace( '+"', '"+', $string);
		$string = str_replace( '-"', '"-', $string);
		foreach($searchIn as $field){
			$string = str_replace( $field.':"', '"'.$field.':', $string);
		}
		preg_match_all('/"([^"]*)"/', $string, $results);		
		if(!empty($results[1])){
			$searchBy = $results[1];
			$string = str_replace($results[0], '', $string);
		}		
		$byCharacter = explode(',', $string);
		$byCharacter = count($byCharacter) == 1 ? explode(' ', $string) : $byCharacter;		
		$searchBy = array_merge( $byCharacter, $searchBy );
		//_Catalogs::pr($byCharacter);	
		foreach($searchBy as $value){
			$key = 0;
			if(!empty($value)){
				$value = trim($value);
				$byField = explode(':', $value);							
				$value = trim($byField[1]);
				if(!empty($value)){
					if(in_array($byField[0], $searchIn)){						
						if(($value[0] != '+') && $value[0] != '-'){
							$value = '+'.$value;
						}
						$fields = array($byField[0]);
					}
				}
									
				if(!empty($value)){								
					if(($value[0] == '+') || $value[0] == '-'){
						($value[0] == '+') ? $sql .= $this->appendAnd( str_replace('+', '', $value) , $fields) : false;
						($value[0] == '-') ? $sql .= $this->appendAnd( str_replace('-', '', $value) , $fields, false ) : false;				
					}		
				}
			}
		}
		return $sql;	
	}
	
	function logSearching($search, $total_rows, $uid){
		$sql = "
		INSERT INTO log_searching(search, total_rows, uid, created) VALUES('".$search."', ".$total_rows.", ".$uid.", NOW())
		";
		return Database::executeConn($sql, 'pilares');
	}
	
}

?>
