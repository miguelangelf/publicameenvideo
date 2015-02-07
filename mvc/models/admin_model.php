<?php 

class admin_model{
	
	const COMPANY_USER = 5;
	const COMPANY_USER_ADMIN = 8;
	const GOVERNMENT_USER = 6;
	const ADMIN_USER = 7;
	const DISABLED_USER = 2;
	const ACTIVE_USER = 1;
	const INACTIVE_USER = 0;
	const ACTIVE_COMPANY = 1;
	const INACTIVE_COMPANY = 2;
	const HIDDEN_COMPANY = 0;
	const ACTIVE_OFFICE = 1;
	const INACTIVE_OFFICE = 0;
	
	public function findCompany( $id = 0, $params = array() ){
		
		$companies = array();
		
		$sql = "
		SELECT SQL_CALC_FOUND_ROWS C.*, DE.name AS delegation, CO.name AS colony
		FROM companies C
		LEFT JOIN delegations DE ON C.id_delegation = DE.id
		LEFT JOIN colonies CO ON C.id_colony = CO.id
		";
		
		$sql .= (isset($params['status']) && $params['status'] != '') ? " WHERE C.status = ".$params['status']." " : " WHERE (C.status = ".self::ACTIVE_COMPANY." OR C.status = ".self::HIDDEN_COMPANY.") ";
		$sql .= (!empty($id)) ? " AND C.id = ".$id." " : "";
		$sql .= (!empty($params['name'])) ? " AND C.name LIKE '%".$params['name']."%'" : '';
		
		if(!empty($params['page']) && !empty($params['rows']) && is_numeric($params['page']) && is_numeric($params['rows'])){
			$sql .=	" LIMIT ".(($params['page']-1)*$params['rows']).", ".$params['rows']." ";
		}
		
		$conn = Database::getExternalConnection("pilares");
        $pdo = $conn->query($sql);
		$pdo2 = $conn->query("SELECT FOUND_ROWS() AS companies");					
		$count = Database::fetch_assoc($pdo2);
		unset($conn);
		while($row = Database::fetch_array($pdo)){
            $companies['company'][] = $row;
        }
		if(!empty($companies)){
			$companies['total_rows'] = $count['companies'];
		}
		
        return $companies;
		
	}
	
	public function findOffices( $id = 0, $params = array() ){
		
		$offices = array();
		
		$sql = "
		SELECT SQL_CALC_FOUND_ROWS * 
		FROM offices
		WHERE active = ".self::ACTIVE_OFFICE."
		";
		
		$sql .= (!empty($id)) ? " AND id = ".$id." " : "";
		$sql .= (!empty($params['name'])) ? " AND name LIKE '%".$params['name']."%'" : '';
		
		if(!empty($params['page']) && !empty($params['rows']) && is_numeric($params['page']) && is_numeric($params['rows'])){
			$sql .=	" LIMIT ".(($params['page']-1)*$params['rows']).", ".$params['rows']." ";
		}
		
		$conn = Database::getExternalConnection("pilares");
        $pdo = $conn->query($sql);
		$pdo2 = $conn->query("SELECT FOUND_ROWS() AS offices");					
		$count = Database::fetch_assoc($pdo2);
		unset($conn);
		while($row = Database::fetch_array($pdo)){
            $offices['office'][] = $row;
        }
		if(!empty($offices)){
			$offices['total_rows'] = $count['offices'];
		}
		
        return $offices;
		
	}
	
	public function saveOffice( $office ){
		
		$fields = array(
			'name', 
			'workstations', 
			'created', 
			'id_delegation', 
			'id_colony', 
			'zip_code', 
			'number', 
			'address', 
			'phone', 
			'lat', 
			'lon',
			'type'
		);
		
		if(!empty($office['id'])){
			
			$rows = array();		
			foreach($fields as $field){
				if(!empty($office[$field])){
					$rows[] = "
					".$field." = '".$office[$field]."'";
				}
			}
			if(!empty($rows)){

				$sql ="
				UPDATE offices
				SET ".implode(", ", $rows)."
				WHERE id = ".$office['id']."
				";
				return Database::executeConn($sql,"pilares");
			}
		}else{
			
			$rows = array();
			$values = array();
			foreach($fields as $field){
				if(!empty($office[$field])){
					$rows[$field] = $field; 
					$values[$field] = "'".$office[$field]."'";
				}
			}
			
			if(!empty($rows)){							
				$sql = "INSERT INTO offices(".implode(', ',$rows).", active, created) VALUES(".implode(', ', $values).", 1, NOW())";
				$conn = Database::getExternalConnection("pilares");
				$pdo = $conn->query($sql);
				$pdo2 = $conn->query("SELECT LAST_INSERT_ID() as id");
				$data =  Database::fetch_row($pdo2);					
				if(!empty($data['id'])){
					
					if(!empty($values['id_delegation'])){
						$access_code = strlen($office['id_delegation']) == 1 ? "0".$office['id_delegation'] : $office['id_delegation'];
					}else{
						$access_code = '00';
					}
					$access_code = $access_code.str_pad( ''.$data['id'], 7, "0", STR_PAD_LEFT);
					
					$sql = "
					UPDATE offices 
					SET access_code = '".$access_code."'
					WHERE id = ".$data['id']."
					";
					Database::executeConn($sql,"pilares");
					return $data['id'];					
				}else{
					return false;
				}		
			}else{
				return false;
			}
		}
		
		return Database::executeConn($sql,"pilares");
		
	}
	
	
	public function saveCompany( $company ){
		
		$fields = array(
			'name',
			'street', 
			'zip_code', 
			'id_delegation', 
			'id_colony', 
			'outside_number', 
			'internal_number', 
			'between_streets', 
			'phone_number', 
			'cell_number', 
			'rfc', 
			'register_name', 
			'tax_regime', 'id_grup', 
			'id_sector', 
			'id_subsector',
			'director_name',
			'director_last_name',
			'director_email',
			'director_phone_number',
			'director_cell_number',
			'director_gender',
			'director_birthdate',
			'website',
			'mission',
			'description',
			'company_size',
			'subsidiaries',
			'presence',
			'career_site',
			'available_users',
			'career_site_title',
			'career_site_description',
			'logo'
			
		);
		
		if(!empty($company['id'])){
			$rows = array();		
			foreach($fields as $field){
				if(!empty($company[$field])){
					$rows[] = "
					".$field." = '".$company[$field]."'";
				}
			}
			if(!empty($rows) && !empty($company['id'])){
				$sql ="
				UPDATE companies
				SET ".implode(", ", $rows)."
				WHERE id = ".$company['id']."
				";
				return Database::executeConn($sql,"pilares");
			}else{
				return false;
			}
		}else{
			$rows = array();
			$values = array();
			foreach($fields as $field){
				if(!empty($company[$field])){
					$rows[$field] = $field; 
					$values[$field] = "'".$company[$field]."'";
				}
			}
			if(!empty($rows)){
				$sql = "INSERT INTO companies(".implode(', ',$rows).", created) VALUES(".implode(', ', $values).", NOW())";
				$conn = Database::getExternalConnection("pilares");
				$pdo = $conn->query($sql);
				$pdo2 = $conn->query("SELECT LAST_INSERT_ID() as id");
				$data =  Database::fetch_row($pdo2);					
				if(!empty($data['id'])){
					return $data['id'];					
				}else{
					return false;
				}		
			}else{
				return false;
			}
		}
				
	}
	
	
	function findCompanyBy( $name ){
		
		$companies = array();
		
		$sql = "
		SELECT id, name
		FROM companies
		WHERE name LIKE '%".$name."%'
		";
		
		$pdo = Database::executeConn($sql,"pilares");
		if($pdo !== false){
			while($row = Database::fetch_array($pdo)){
				$companies[] = $row;
			}
		}
		
		return $companies;
		
	}
	
	function findUsers( $conditions = array() ){
		
		$data = array();
		
		$sql = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM us_users U
		INNER JOIN us_roles R ON U.uid = R.uid
		AND (
			R.rid = ".self::COMPANY_USER."
			OR R.rid = ".self::COMPANY_USER_ADMIN."
			OR R.rid = ".self::GOVERNMENT_USER."
			OR R.rid = ".self::ADMIN_USER."
		)
		AND (
			U.id_status = ".self::ACTIVE_USER."
			OR U.id_status = ".self::INACTIVE_USER." 
		)
		";
		
		if(!empty($conditions)){
			if(!is_array($conditions)){ 
				$sql .= "WHERE U.uid = ".$conditions." ";
			}else{
				if(!empty($conditions['name'])){
					$sql .= "
					WHERE U.name LIKE '%".$conditions['name']."%' 
					OR U.last_name LIKE '%".$conditions['name']."%' 
					OR U.email LIKE '%".$conditions['name']."%' 
					";	
				}
				if(!empty($conditions['pagination'])){
					$params = $conditions['pagination'];
					if(!empty($params['page']) && !empty($params['rows']) && is_numeric($params['page']) && is_numeric($params['rows'])){
						$sql .=	" 
						ORDER BY U.name, U.last_name 
						LIMIT ".(($params['page']-1)*$params['rows']).", ".$params['rows']." 
						";
					}	
				}				
			}
		}
		
		//echo $sql;
		$conn = Database::getConnection();
        $pdo = $conn->query($sql);
		$pdo2 = $conn->query("SELECT FOUND_ROWS() AS users");					
		$count = Database::fetch_assoc($pdo2);
		unset($conn);
		while($row = Database::fetch_array($pdo)){
			$row['assoc'] = $this->getInfoUser($row['uid'], $row['rid']);			
            $data['users'][] = $row;
        }
		if(!empty($data)){
			$data['total_rows'] = $count['users'];
		}
		
        return $data;
		
	}
	
	
	function getInfoUser( $uid, $rid ){
		
		$data = false;				
		
		switch($rid){
			case self::COMPANY_USER:
			case self::COMPANY_USER_ADMIN:
				$sql = $this->companyUser($uid, $rid);
			break;
			case self::GOVERNMENT_USER:
				$sql = $this->governmentUser($uid);
			break;
			case self::ADMIN_USER:
				$data['type_user'] = 'Administrador';
			break;
		}
		
		if(!empty($sql)){
			$pdo = Database::executeConn($sql,"pilares");
			if($pdo !== false){
				return Database::fetch_row($pdo);
			}else{
				return false;
			}
		}else{
			return $data;	
		}
		
	}	
	
	private function companyUser( $uid, $rid ){
		$type = $rid ==  self::COMPANY_USER_ADMIN ? ' Admin.' : '';
		$sql = "
		SELECT U.*, 'Empresa".$type."' AS type_user, C.name AS entity_name 
		FROM users_companies U
		INNER JOIN companies C ON C.id = U.id_company
		WHERE U.uid = ".$uid."
		";
		
		return $sql;				
	}
	
	private function governmentUser( $uid ){
		$sql = "
		SELECT *, 'Enlace' AS type_user
		FROM users_offices U
		INNER JOIN offices O ON O.id = U.id_office
		WHERE U.uid = ".$uid."
		";
		
		return $sql;	
	}
	
	function updateCompanyUser( $user ){
		
		$fields = array(			
			'name',
			'last_name',
			'email',
			'birthdate',
			'gender',
			'phone_number',
			'cell_number',
			'id_company',
			'picture',
			'position'
		);
		
		$rows = array();
		
		foreach($fields as $field){
			if(!empty($user[$field])){
				$rows[] = "
				".$field." = '".$user[$field]."'";
			}
		}
		
		if(!empty($rows) && !empty($user['uid'])){
			$sql = "
			UPDATE users_companies 
			SET ".implode(", ", $rows)." 
			WHERE uid = ".$user['uid']." 
			";
			$pdo = Database::executeConn($sql,"pilares");			
			if($pdo !== false){
				return $this->updateUser($user);
			}
		}else{
			return false;
		}
		
	}
	
	
	function updateUser( $user ){
		
		$fields = array(			
			'name',
			'last_name',
			'email'			
		);
		
		$rows = array();
		
		foreach($fields as $field){
			if(!empty($user[$field])){
				$rows[] = "
				".$field." = '".$user[$field]."'";
			}
		}
		
		if(!empty($rows) && !empty($user['uid'])){
			$sql = "
			UPDATE us_users 
			SET ".implode(", ", $rows)." 
			WHERE uid = ".$user['uid']." 		
			";
			
			$pdo = Database::execute($sql);			
			if($pdo !== false){
				$sql = "
				UPDATE us_roles
				SET rid = ".$user['rid']."
				WHERE uid = ".$user['uid']." 
				";
				return Database::execute($sql);
			}else{
				return false;
			}
		}else{
			return false;	
		}				
		
	}
	
	function dataGovernmentUser( $user ){
		
		$fields = array(
			'uid',
			'id_office'
		);
		
		$rows = array();
		$values = array();
		foreach($fields as $field){
			if(!empty($user[$field])){
				$rows[$field] = $field; 
				$values[$field] = "'".$user[$field]."'";
			}
		}
		if(!empty($rows)){
			$sql = "INSERT INTO users_offices(".implode(', ',$rows).", created) VALUES(".implode(', ', $values).", NOW())";	
			return Database::executeConn($sql,"pilares");
		}
		
	}
	
	function updateGovernmentUser( $user ){
		
		$fields = array(
			'uid',
			'id_office'
		);
		
		$rows = array();
		
		foreach($fields as $field){
			if(!empty($user[$field])){
				$rows[] = "
				".$field." = '".$user[$field]."'";
			}
		}
		
		if(!empty($rows) && !empty($user['uid'])){
			$sql = "
			UPDATE users_offices 
			SET ".implode(", ", $rows)." 
			WHERE uid = ".$user['uid']." 		
			";
			
			$pdo = Database::executeConn($sql,"pilares");			
			if($pdo !== false){
				return $this->updateUser($user);
			}else{
				return false;
			}			
		}else{
			return false;	
		}
		
	}
	
	
	function disableUser( $uid ){
		
		$sql = "
		UPDATE us_users
		SET id_status = ".self::DISABLED_USER.",
		email = CONCAT(email, '.deleted_user'),
		token = '' 		
		WHERE uid = ".$uid." 
		";
		
		return Database::execute($sql);
		
	}
	
	
	function disableCompany( $id_company ){		
		return $this->statusCompany( $id_company, self::INACTIVE_COMPANY);				
	}
	
	
	function disableUserByAssoc($rid, $uids){
		return $this->setStatusUsersByAssoc( $rid, $uids, self::DISABLED_USER);			
	}
	
	
	function disableOffice( $id_office ){
		
		$users = array();
		
		$sql = "
		UPDATE offices
		SET active = ".self::INACTIVE_OFFICE."
		WHERE id = ".$id_office."
		";
		
		$pdo = Database::executeConn($sql,"pilares");
		if($pdo !== false){
			
			$sql = "
			SELECT *
			FROM users_offices
			WHERE id_office = ".$id_office."
			";
			
			$pdo = Database::executeConn($sql,"pilares");
			if($pdo !== false){
				while($row = Database::fetch_array($pdo)){
					$users[] = $row['uid'];
				}
				return $this->disableUserByAssoc( self::GOVERNMENT_USER, $users );
			}else{
				return false;
			}
			
		}else{
			return false;
		}
			
	}
	
	
	function statusCompany( $id_company, $status){
		
		$users = array();
		
		$sql = "
		UPDATE companies
		SET status = ".$status."
		WHERE id = ".$id_company."
		";
		
		$pdo = Database::executeConn($sql,"pilares");
		if($pdo !== false){
			
			$sql = "
			SELECT *
			FROM users_companies
			WHERE id_company = ".$id_company."
			";
			
			$pdo = Database::executeConn($sql,"pilares");
			if($pdo !== false){
				while($row = Database::fetch_array($pdo)){
					$users[] = $row['uid'];
				}
				$status_users = ($status == self::ACTIVE_COMPANY) ? self::ACTIVE_USER : self::INACTIVE_USER;
				$this->setStatusUsersByAssoc( self::COMPANY_USER, $users, $status_users );
				return $this->setStatusUsersByAssoc( self::COMPANY_USER_ADMIN, $users, $status_users );
			}else{
				return false;
			}
			
		}else{
			return false;
		}
		
	}
	
	function setStatusUsersByAssoc( $rid, $uids, $status){
		
		if(!empty($uids)){
			$sql = "
			UPDATE us_users U
			INNER JOIN us_roles R ON U.uid = R.uid
			AND R.rid = ".$rid."
			SET U.id_status = ".$status."
			WHERE U.uid IN (".implode(', ', $uids).") 
			";
			
			return Database::execute($sql);
			
		}else{
			return true;	
		}
		
	}
	
	
	function getTimeSlots( $id_office = false ){
		
		$slots = array();
		
		if(!empty($id_office)){
			$sql = "
			SELECT T.id, T.time, O.id_office, COUNT(I.id) AS used
			FROM time_slots T 
			LEFT JOIN  `office_to_time_slots` O ON T.id = O.id_time_slot  
			AND id_office = ".$id_office."
                        LEFT JOIN interviews_offices I ON I.id_office_to_time_slot = O.id
                        GROUP BY T.id
			ORDER BY T.time
			";
		}else{			
			$sql = "
			SELECT T.time AS time, T.id, COUNT(O.id_time_slot) AS used
			FROM  time_slots T
                        LEFT JOIN  `office_to_time_slots` O ON T.id = O.id_time_slot
                        GROUP BY T.id
			ORDER BY T.time				
			";				
		}
		
		$pdo = Database::executeConn($sql,"pilares");
		if($pdo !== false){
			while($row = Database::fetch_array($pdo)){
				$slots[] = $row;
			}			
		}
		
		return $slots;
		
	}
	
	
	function timeSlots( $data ){
			
		$sql = "DELETE FROM office_to_time_slots";
		$sql .= is_numeric($data['id_office']) ? " WHERE id_office = ".$data['id_office']." " : "";
		$pdo = Database::executeConn($sql,"pilares");
		if($pdo !== false){
			if(!empty($data['slot'])){
                            
                            $id_office = is_numeric($data['id_office']) ? $data['id_office'] : false; 
                            $offices = $this->findOffices($id_office);
			
                            foreach($offices['office'] as $office){
				for($ws = 0; $ws < $office['workstations']; $ws ++){
                                    $sql = "
                                    INSERT INTO office_to_time_slots(id_office, id_time_slot)
                                    SELECT 
                                    ".$office['id']." AS id_office,
                                    id AS id_time_slot
                                    FROM time_slots
                                    WHERE id IN(".implode(',', $data['slot']).")
                                    ";
                                    Database::executeConn($sql,"pilares");
                                }
                            }
                            
                            return true;
								
				
			}			
			return true;			
		}			
		return false;								
		
	}
	
	
	function findOfficesHoliDays( $data ){
		
		$rows = array();				
		
		$sql = "
		SELECT *
		FROM `calendar` C 
		INNER JOIN interviews_offices I ON I.id = C.id_table  
		INNER JOIN office_to_time_slots O ON I.id_office_to_time_slot = O.id
		WHERE O.id_office = ".$data['id_office']."
                AND I.id_candidate = 0
		";
		
		$sql .= !empty($data['year']) ?  " AND C.date = '".$data['year']."-".$data['month']."-".$data['day']."' " : " GROUP BY C.date ORDER BY C.date ASC";
		
		$pdo = Database::executeConn($sql,"pilares");
		
		if($pdo !== false){
			while($row = Database::fetch_array($pdo)){
				!empty($data['year']) ? $rows[$row['id_time_slot']] = true : $rows['holiday'][] = $row;
			}			
		}
		
		return $rows;
		
	}
	
	
	function saveHoliDays( $holiday ){
		
		$sql = "
		DELETE C, I 
		FROM calendar C
		INNER JOIN interviews_offices I ON C.id_table = I.id
		AND id_candidate = 0
		AND C.`date` = '".$holiday['year']."-".$holiday['month']."-".$holiday['day']."'
		AND C.id_calendar_type =1		 
		";
		
		if(is_numeric($holiday['id_office'])){
			$sql .= " 
			INNER JOIN office_to_time_slots O ON I.id_office_to_time_slot = O.id 
			AND O.id_office = ".$holiday['id_office']." 
			"; 
		}
		
		Database::executeConn($sql,"pilares");                                               
		
		if(!empty($holiday['slot'])){
			
			$id_office = is_numeric($holiday['id_office']) ? $holiday['id_office'] : false; 
			$offices = $this->findOffices($id_office);                                                
			
			foreach($offices['office'] as $office){
				
					$sql = "
					INSERT INTO interviews_offices(id_office_to_time_slot, created, type )
					SELECT O.id, NOW(), '".$office['type']."' 
					FROM office_to_time_slots O					
					WHERE O.id_time_slot IN(".implode(',', $holiday['slot']).")
					AND O.id_office = ".$office['id']." ";
					
					$conn = Database::getExternalConnection("pilares");
					$pdo = $conn->query($sql);
					$pdo2 = $conn->query("SELECT LAST_INSERT_ID() AS id, ROW_COUNT() AS number");
					$data =  Database::fetch_row($pdo2);                                        
					if(!empty($data['id'])){
                                                $sql = "
						INSERT INTO calendar(id_calendar_type, date, id_table) VALUES
						";
                                                $record = false;
						for($id = $data['id']; $id < ($data['id']+$data['number']); $id++ ){
                                                    if($record){
                                                        $sql .= ", ";                                                        
                                                    }
                                                    $sql .= "(1, '".$holiday['year']."-".$holiday['month']."-".$holiday['day']."', ".$id.")";
                                                    $record = true;
						}
                                                
                                                Database::executeConn($sql,"pilares");
					}		
				
			}						

		}
		
		return true;
	}


	function manageSlot( $slot ){
		if($slot['action'] == 'add'){
			$sql = "INSERT INTO time_slots(time) VALUES('".$slot['hour'].":".$slot['minute']."')";
		}
		if($slot['action'] == 'delete'){
			$sql = "DELETE FROM time_slots WHERE id = ".$slot['id']." ";
		}
		if(!empty($sql)){
			return Database::executeConn($sql,"pilares");
		}else{
			return false;
		}
	}
        
        
        function getCPS( $id_office ){
            
            $cps = array();
            
            $sql = "
            SELECT O.office_id, O.cp, D.name AS delegation 
            FROM offices_cps O
            INNER JOIN colonies C ON C.cp = O.cp
            INNER JOIN delegations D ON D.id = C.id_delegation
            GROUP BY O.cp
            ORDER BY O.cp ASC
            ";
            $pdo = Database::executeConn($sql,"pilares");
            while($row = Database::fetch_array($pdo)){
                $cps[] = $row;
            }
            
            return $cps;
            
        }
        
        
        function saveOfficeCPS( $data ){
            
            $sql = "
            UPDATE offices_cps
            SET office_id = ".$data['id_office']."
            WHERE cp IN(".implode(',', $data['cp']).")    
            ";
            
            return Database::executeConn($sql,"pilares");
        }
        
        
        function getRoles($uid){
            $sql = "SELECT rid FROM us_roles WHERE uid=$uid";
            $pdo = Database::execute($sql);
            if($pdo !== false){
                return  Database::fetch_row($pdo); 
            }else{
                return false;
            }
        }


        function setStatus($uid){
        	if(!empty($uid)){
				$sql = "
				UPDATE us_users U				
				SET U.id_status = 1
				WHERE U.uid = ".$uid." 
				";
				
				return Database::execute($sql);
			}		
        }


        function companyUsers($companies, $params = array()){

        	$users_companies = array();
			$paginate = false;

        	$sql = "
            SELECT SQL_CALC_FOUND_ROWS users_companies.*, companies.name as company_name
            FROM users_companies
            INNER JOIN companies ON users_companies.id_company = companies.id 
			WHERE users_companies.active = 1                      
            ";

            $sql .= is_array($companies)? "AND id_company IN (".implode(',', $companies).") " : "AND id_company = ".$companies." ";
			
			if(!empty($params['page']) && !empty($params['rows']) && is_numeric($params['page']) && is_numeric($params['rows'])){
				$sql .=	"LIMIT ".(($params['page']-1)*$params['rows']).", ".$params['rows']." ";
				$paginate = true;
			}		
            //_Catalogs::pr($sql);
			if($paginate == false){
				$pdo = Database::executeConn($sql,"pilares");
				while($row = Database::fetch_array($pdo)){
					$users_companies[] = $row;
				}
			}else{
				$conn = Database::getExternalConnection("pilares");
				$pdo = $conn->query($sql);
				$pdo2 = $conn->query("SELECT FOUND_ROWS() AS total_rows");					
				$count = Database::fetch_assoc($pdo2);
				unset($conn);
				$users_companies['total_rows'] = $count['total_rows'];
				while($row = Database::fetch_array($pdo)){
					$users_companies['rows'][] = $row;
				}
			}
			
            return $users_companies;

        }
		
		
		function inactiveCompanyuser( $uid ){
			
			if(!empty($uid)){
				$sql = "
				UPDATE users_companies U				
				SET U.active = 0
				WHERE U.uid = ".$uid." 
				";
				
				return Database::executeConn($sql,"pilares");
			}
			
		}

	
}

?>