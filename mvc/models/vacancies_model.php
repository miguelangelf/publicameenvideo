<?php

class vacancies_model{

	function find( $params ){
		
		$_sql_search = "";
		if(isset($params["search"])){
            $_sql_search = ($params["search"] == "") ? "" : " AND (V.position LIKE '%".$params["search"]."%' OR U.name LIKE '%".$params["search"]."%' OR U.last_name LIKE '%".$params["search"]."%')";
        }
			
		$records = array();
		$sql = "
		SELECT
			SQL_CALC_FOUND_ROWS 
			V.*,
			C.city_name,
			S.subdivision_1_name,
			CO.country_name,
			U.name,
			U.last_name,
			U.picture,
			U.uid AS uid_manager,
			L.uid AS revised 
		FROM vacancies V
		LEFT JOIN users_companies U ON U.uid = V.uid
		LEFT JOIN ".Config::get("Theme.Databases.geo.name").".cities C ON V.id_city = C.geoname_id
		AND C.locale_code = 'es'
		LEFT JOIN  ".Config::get("Theme.Databases.geo.name").".states S ON V.id_state = S.geoname_id
		AND S.locale_code = 'es'
		LEFT JOIN  ".Config::get("Theme.Databases.geo.name").".countries CO ON V.id_country = CO.country_iso_code
		AND CO.locale_code = 'es'
		LEFT JOIN revised_vacancies L ON L.id_vacancy = V.id
		AND L.uid = ".$params['logged_user']."
		WHERE V.id_company = ".$params['id_company']."
		".$_sql_search."		
		GROUP BY V.id
		ORDER BY V.id DESC		
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
		$records['total_rows'] = $count['total_rows'];		
		while($row = Database::fetch_array($pdo)){
			$row['city_name'] = utf8_encode($row['city_name']);
			$row['subdivision_1_name'] = utf8_encode($row['subdivision_1_name']);
			$row['country_name'] = utf8_encode($row['country_name']);
			$records['rows'][] = $row;
		}
        return $records;

	}

	function findById( $id ){

		$sql = "
		SELECT
			V.*,
			S.subdivision_1_name,
			CO.country_name,
			C.city_name,
			U.email,
			U.phone_number
		FROM vacancies V
		LEFT JOIN users_companies U ON U.uid = V.uid
		LEFT JOIN ".Config::get("Theme.Databases.geo.name").".cities C ON V.id_city = C.geoname_id
		AND C.locale_code = 'es'
		LEFT JOIN  ".Config::get("Theme.Databases.geo.name").".states S ON V.id_state = S.geoname_id
		AND S.locale_code = 'es'
		LEFT JOIN  ".Config::get("Theme.Databases.geo.name").".countries CO ON V.id_country = CO.country_iso_code
		AND CO.locale_code = 'es'
		WHERE V.id = ".$id."
		";
		$pdo = Database::executeConn($sql, 'pilares');		
		if($pdo !== false){
			$row = Database::fetch_row($pdo);
			$row['city_name'] = utf8_encode($row['city_name']);
			$row['subdivision_1_name'] = utf8_encode($row['subdivision_1_name']);
			$row['country_name'] = utf8_encode($row['country_name']);
			if(!empty($row['fid'])){
				$sql = "
				SELECT * 
				FROM sy_files
				WHERE fid = ".$row['fid']."
				";
				
				$pdo = Database::execute($sql);
				$row['file'] = Database::fetch_row($pdo);
			}
			$row['languages'] = $this->findVacancyLanguages( $id );
			$row['industries'] = $this->findVacancyIndustries( $id );
			$row['functions'] = $this->findVacancyFunctions( $id );

			return $row;
		}else{
			return false;
		}

	}


	function findVacancyLanguages( $id_vacancy ){

		$records = array();
		$sql = "
		SELECT
			L.*
		FROM vacancy_languages VL 
		INNER JOIN languages L ON L.id = VL.id_language
		WHERE id_vacancy = ".$id_vacancy."
		";
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[] = $row;
		}

		return $records;

	}

	function findVacancyIndustries( $id_vacancy ){

		$records = array();
		$sql = "
		SELECT
			L.*
		FROM vacancy_industries VL 
		INNER JOIN industries L ON L.id = VL.id_industry
		WHERE id_vacancy = ".$id_vacancy."
		";
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[] = $row;
		}

		return $records;

	}


	function findVacancyFunctions( $id_vacancy ){

		$records = array();
		$sql = "
		SELECT
			L.*
		FROM vacancy_functions VL 
		INNER JOIN functions L ON L.id = VL.id_function
		WHERE id_vacancy = ".$id_vacancy."
		";
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[] = $row;
		}

		return $records;

	}

	public function save($data){

		$fields = array(
			"uid",
			"position",			 
			"job_description", 
			"department",
			"id_company",
			"id_city",
			"zip_code",
			"max_age",
			"min_age",
			"fid",
			"attachment_name",
			"sex",
			"published",
			"id_travel_availability",
			"id_country",
			"id_state",
			"education",
			"id_currency",
			"salary",
			"lat",
			"lng",
			"street",
			"number",
			"marker_ok",
			"id_vacancy_type"
		);
		
		$rows = array();		
			
		if(empty($data['id'])){
			
			foreach($fields as $field){
				if(isset($data[$field])){
					$rows[$field] = $field; 
					$values[$field] = "'".$data[$field]."'";
				}
			}						

			if(!empty($rows)){
				$sql = "
				INSERT INTO vacancies(".implode(', ',$rows).", created, modified) 
				VALUES(".implode(', ', $values).", NOW(), NOW()) 				
				";
				$conn = Database::getExternalConnection("pilares");
				$pdo = $conn->query($sql);
				$pdo2 = $conn->query("SELECT LAST_INSERT_ID() as id");
				$data =  Database::fetch_row($pdo2);
				unset($conn);					
				if(!empty($data['id'])){
					return $data['id'];
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{

			foreach($fields as $field){
				if(isset($data[$field])){
					$rows[] = "
					".$field." = '".$data[$field]."'";
				}
			}
			
			if(empty($data["published"])){
				$rows[] = "
					published = '0'";
			}
			
			if(!empty($rows)){
				$sql = "
				UPDATE vacancies 
				SET ".implode(", ", $rows).",
				modified = NOW() 
				WHERE id = ".$data['id']." ";
				$pdo = Database::executeConn($sql, 'pilares');
				if($pdo !== false){					
					return $data['id'];
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
	}


	function saveLanguages( $data ){

		$sql = "
		DELETE FROM vacancy_languages
		WHERE id_vacancy = ".$data['id']."
		";

		$pdo = Database::executeConn($sql, 'pilares');

		$sql = "
		SELECT 
			*
		FROM languages
		WHERE value IN ('".implode("','", $data['languages'] )."')
		";
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[$row['value']] = $row;			
		}

		foreach ($data['languages'] as $key => $value) {
			if(!isset($records[$value])){
				$sql = "
				INSERT INTO languages(value, status) VALUES('".$value."', 0)
				";
				$conn = Database::getExternalConnection("pilares");
				$pdo = $conn->query($sql);
				$pdo2 = $conn->query("SELECT LAST_INSERT_ID() as id");
				$dataC =  Database::fetch_row($pdo2);
				unset($conn);					
				if(!empty($dataC['id'])){
					$records[$value]['id'] = $dataC['id'];
				}
			}
		}

		foreach ($records as $key => $value) {
			$sql = "
			INSERT INTO vacancy_languages(id_vacancy, id_language) VALUES('".$data['id']."', '".$value['id']."')
			";
			Database::executeConn($sql, 'pilares');	
		}

	}


	function saveIndustries( $data ){

		$sql = "
		DELETE FROM vacancy_industries
		WHERE id_vacancy = ".$data['id']."
		";

		$pdo = Database::executeConn($sql, 'pilares');

		$sql = "
		SELECT 
			*
		FROM industries
		WHERE value IN ('".implode("','", $data['industries'] )."')
		";
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[$row['value']] = $row;			
		}

		foreach ($data['industries'] as $key => $value) {
			if(!isset($records[$value])){
				$sql = "
				INSERT INTO industries(value) VALUES('".$value."')
				";
				$conn = Database::getExternalConnection("pilares");
				$pdo = $conn->query($sql);
				$pdo2 = $conn->query("SELECT LAST_INSERT_ID() as id");
				$dataC =  Database::fetch_row($pdo2);
				unset($conn);					
				if(!empty($dataC['id'])){
					$records[$value]['id'] = $dataC['id'];
				}
			}
		}

		foreach ($records as $key => $value) {
			$sql = "
			INSERT INTO vacancy_industries(id_vacancy, id_industry) VALUES('".$data['id']."', '".$value['id']."')
			";
			Database::executeConn($sql, 'pilares');	
		}

	}



	function saveFunctions( $data ){

		$sql = "
		DELETE FROM vacancy_functions
		WHERE id_vacancy = ".$data['id']."
		";

		$pdo = Database::executeConn($sql, 'pilares');

		$sql = "
		SELECT 
			*
		FROM functions
		WHERE value IN ('".implode("','", $data['functions'] )."')
		";
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[$row['value']] = $row;			
		}

		foreach ($data['functions'] as $key => $value) {
			if(!isset($records[$value])){
				$sql = "
				INSERT INTO functions(value) VALUES('".$value."')
				";
				$conn = Database::getExternalConnection("pilares");
				$pdo = $conn->query($sql);
				$pdo2 = $conn->query("SELECT LAST_INSERT_ID() as id");
				$dataC =  Database::fetch_row($pdo2);
				unset($conn);					
				if(!empty($dataC['id'])){
					$records[$value]['id'] = $dataC['id'];
				}
			}
		}

		foreach ($records as $key => $value) {
			$sql = "
			INSERT INTO vacancy_functions(id_vacancy, id_function) VALUES('".$data['id']."', '".$value['id']."')
			";
			Database::executeConn($sql, 'pilares');	
		}

	}

	public function deleteDocumentVacancy( $fid ){

		if(!empty($fid)){
					
			$sql = "
			SELECT * 
			FROM sy_files F				
			INNER JOIN sy_filecategories C ON C.fcid = F.fcid	
			WHERE F.fid = ".$fid."
			";
			
			$pdo = Database::execute($sql);												
			if($pdo !== false){
				$row = Database::fetch_row($pdo);
				if(!empty($row)){
					$sql = "
					DELETE FROM sy_files
					WHERE fid = ".$row['fid']."
					";
					$pdo = Database::execute($sql);
					if($pdo !== false){
						$theme_path = Config::get('Theme.Web.uploads');
						$file = $theme_path.$row['path'].$row['file_name'];										
						unlink($file);																	
					}else{
						return false;									
					}
				}
			}else{
				return false;							
			}
		}

	}

	function findLanguages( $text ){

		$records = array();

		$sql = "
		SELECT 
			id,
			value 
		FROM languages
		WHERE value LIKE '%".$text."%'
		AND status = 1
		LIMIT 5
		";

		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[] = $row['value'];
		}

		return $records;
	}


	function findIndustries( $text ){

		$records = array();

		$sql = "
		SELECT 
			id,
			value 
		FROM industries
		WHERE value LIKE '%".$text."%'
		AND status = 1
		LIMIT 5
		";

		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[] = $row['value'];
		}

		return $records;
	}


	function findFunctions( $text ){

		$records = array();

		$sql = "
		SELECT 
			id,
			value 
		FROM functions
		WHERE value LIKE '%".$text."%'
		AND status = 1
		LIMIT 5
		";

		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[] = $row['value'];
		}

		return $records;
	}


	function findCountries(){

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


	function findStates( $country_iso_code = 'MX'){
		$records = array();
		$sql = "
		SELECT
			geoname_id as geoname_id,
			subdivision_1_name
		FROM states S
		WHERE locale_code = 'en' 		
		AND subdivision_1_name != ''
		AND country_iso_code = '".$country_iso_code."'
		ORDER BY subdivision_1_name
		";
		$pdo = Database::executeConn($sql, 'geo');
		while($row = Database::fetch_array($pdo)){			
			$records[$row['geoname_id']] = utf8_encode($row['subdivision_1_name']);
		}

		return $records;	
	}


	function findCities( $id_country, $id_state, $city = '' ){
		$records = array();
		
		if(!empty($id_country) || !empty($id_state) || !empty($city)){
			$sql = "
			SELECT
				geoname_id,			
				".( !empty($city) ? "city_name, subdivision_1_name AS state" : "city_name" )."
			FROM cities C
			WHERE locale_code = 'es' 				
			".( !empty($id_country) ? "AND country_iso_code = '".$id_country."' " : "" )."
			".( !empty($id_state) ? "AND subdivision_1_name = '".$id_state."' " : "" )."
			".( !empty($city) ? "AND city_name LIKE '%".$city."%' " : "" )."
			AND city_name != ''
			ORDER BY city_name
			".( !empty($city) ? "LIMIT 30" : "" )."
			";
			$pdo = Database::executeConn($sql, 'geo');
			while($row = Database::fetch_array($pdo)){			
				if(empty($city)){ 
					$records[$row['geoname_id']] = utf8_encode($row['city_name']);
				}else{				
					$records[] = array('id' => utf8_encode($row['city_name']), 'city' => utf8_encode($row['city_name']), 'state' => utf8_encode($row['state']));				
				}
			}
		}else{
			$records[0] = array('id' => ' ', 'city' => 'Todo México', 'state' => '');
		}

		return $records;	
	}


    function publish($id_vacancy, $status) {
        
        $sql = "
        UPDATE vacancies
        SET published = ".$status.",
        modified = NOW()
        WHERE id = ".$id_vacancy."        
		";

		return Database::executeConn($sql, 'pilares');

    }
	
	function revisedByUid( $id_vacancy, $uid ){
		$sql = "
        INSERT INTO revised_vacancies( id_vacancy, uid, created ) VALUES( ".$id_vacancy.", ".$uid.", NOW() )       
		";

		return Database::executeConn($sql, 'pilares');
	}
	
	function findByUser( $uid ){
		
		$records = array();
		
		$sql = "
		SELECT 
			id,
			position
		FROM vacancies
		WHERE uid = ".$uid."
		";
		
		$pdo = Database::executeConn($sql, 'pilares');
		while($row = Database::fetch_array($pdo)){			
			$records[] = $row;
		}
		
		return $records;
	}
	
	function assign( $vacancies, $uid ){
		
		$sql = "
		UPDATE vacancies
		SET uid = ".$uid.",
		modified = NOW()
		WHERE id IN(".implode(',', $vacancies).")
		";
		
		return Database::executeConn($sql, 'pilares');
		
	}
	
	function findPublished( $params ){
		
		$_sql_search = empty($params["search"]) ? "" : " AND (V.position LIKE '%".$params["search"]."%' OR U.name LIKE '%".$params["search"]."%' OR U.last_name LIKE '%".$params["search"]."%')";
		$_sql_applied = !empty($params["uid"]) ? "LEFT JOIN vacancy_candidates VC ON VC.id_vacancy = V.id AND VC.id_candidate = ".$params["uid"]." " : ""; 				
			
		$records = array();
		$sql = "
		SELECT
			SQL_CALC_FOUND_ROWS 
			V.*,
			C.city_name,
			S.subdivision_1_name,
			CO.country_name
			".( !empty($params["uid"]) ? ", VC.id_candidate AS applied" : "" )."			 
		FROM vacancies V		
		LEFT JOIN ".Config::get("Theme.Databases.geo.name").".cities C ON V.id_city = C.geoname_id
		AND C.locale_code = 'es'
		LEFT JOIN  ".Config::get("Theme.Databases.geo.name").".states S ON V.id_state = S.geoname_id
		AND S.locale_code = 'es'
		LEFT JOIN  ".Config::get("Theme.Databases.geo.name").".countries CO ON V.id_country = CO.country_iso_code
		AND CO.locale_code = 'es'
		".$_sql_applied."		
		WHERE V.id_company = ".$params['id_company']."
		AND V.published = 1
		".$_sql_search."				
		GROUP BY V.id
		ORDER BY V.modified DESC		
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
		$records['total_rows'] = $count['total_rows'];		
		while($row = Database::fetch_array($pdo)){
			$row['city_name'] = utf8_encode($row['city_name']);
			$row['subdivision_1_name'] = utf8_encode($row['subdivision_1_name']);
			$row['country_name'] = utf8_encode($row['country_name']);
			$row['job_description'] = strip_tags($row['job_description']);
			$row['job_description'] = (substr($row['job_description'], 0, 310)).( strlen($row['job_description']) > 310 ? ' ... <span class="show-more">[ver más]</span>' : '' );
			$records['rows'][] = $row;
		}
        return $records;

	}
	
	function applyTo( $data ){
		
		$sql = "
		INSERT INTO vacancy_candidates(
			`id_candidate`,
			`id_vacancy`,
			`external`,
			`created`
		)
		VALUES(
			".$data['uid'].",
			".$data['vacancy_id'].",
			".$data['external'].",
			NOW()
		)
		";
		
		return Database::executeConn($sql, 'pilares');
	}	
	
	function applied( $id_vacancy, $id_candidate ){
		$sql = "
		SELECT * 
		FROM vacancy_candidates
		WHERE id_candidate = ".$id_candidate."
		AND id_vacancy = ".$id_vacancy."
		";
		
		$pdo = Database::executeConn($sql,"pilares");
		$row = Database::fetch_row($pdo);;
		return $row;
	}

}

?>