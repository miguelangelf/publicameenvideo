<?php 

/**
* 
*/
class registro_model{

	const ROL_CANDIDATE = 4;
	const ROL_COMPANY = 5;
	const ERROR_EMAIL = -1;
	const ERROR_CURP = -2;
	const ACTIVE_CANDIDATE = 1;
	
	function __construct(){
		# code...
	}

	function addCandidate($candidate){

		/*
		foreach ($candidate as $key => $value) {
            if (empty($value)) {
                return false;
            }
        }
		*/
		
		$valid = $this->validate($candidate);
		if( $valid != true ){
			return $valid;	
		}
		
		$candidate['rol'] = self::ROL_CANDIDATE;
		$uid = Users::add($candidate);
		if(is_numeric($uid)){
			if($uid > 0){
				$candidate['uid'] = $uid;
				$result = $this->createCandidate( $candidate );
				if($result === false){
					return false;
				}else{
					return $uid;
				}				
			}else{
				return $uid;
			}
		}else{
			return false;
		}

	}
	
	private function createCandidate( $candidate ){
		
		if(!empty($candidate['day']) && !empty($candidate['month']) && !empty($candidate['year'])){
			$candidate['birthdate']	= $candidate['year'].'-'.$candidate['month'].'-'.$candidate['day'];
		}
		if(!empty($candidate['street']) && !empty($candidate['number'])){
			$candidate['address'] = $candidate['street'].' #'.$candidate['number'];
		}
		$fields = array(
			'uid',
			'name',
			'last_name',
			'email',
			'zip_code',
			'address',
			'birthdate',
			'gender',
			'phone_number',
			'cell_number',
			'id_city',
			'id_state',
			'id_country'	
		);
		
		$rows = array();
		
		foreach($fields as $field){
			if(!empty($candidate[$field])){
				$rows[$field] = $field; 
				$values[$field] = "'".$candidate[$field]."'";
			}
		}
		
		if(!empty($rows)){
			$sql = "
			INSERT INTO candidates(".implode(', ',$rows).", created) VALUES(".implode(', ', $values).", NOW())
			";
			
			return Database::executeConn($sql,"pilares");
		}else{
			return false;
		}
		
	}
	
	public function validate( $candidate ){
		
		$sql = "
		SELECT uid, email
		FROM candidates
		WHERE email = '".Database::sanitizer($candidate['email'])."'		
		";
		
		$pdo = Database::executeConn($sql,"pilares");
		$data = Database::fetch_row($pdo);
		
		if(!empty($data['email']) && $data['email'] == $candidate['email']){
			return self::ERROR_EMAIL;
		}
				
		return true;
		
	}
	
	public function validateCURP( $candidate ){
		
		if(!empty($candidate['curp'])){
			$valid = Validator::validate($candidate['curp'], 'CURP', array('CURP' => 'Ingresa un CURP valido'));				
			
			if(empty($valid)){
				$sql = "
				SELECT *
				FROM candidates
				WHERE curp = '".Database::sanitizer($candidate['curp'])."'
				AND uid != ".$candidate['uid']."		
				";
				
				$pdo = Database::executeConn($sql,"pilares");
				$data = Database::fetch_row($pdo);						
				if(!empty($data['curp'])){
                                    return self::ERROR_CURP;	
				}else{
                                    return true;
                                }
			}else{
				return false;
			}	
		}else{
                    return false;
                }
	}


	function getBirthdate($curp){
		$year = substr($curp, 4, 2);
		$month = substr($curp, 6, 2);
		$day = substr($curp, 8, 2);

		$currentYear = date('y');		
		$fullYear = $year > $currentYear ? ($year+1900) : (2000+$year);	
		$birthdate = $fullYear.'-'.$month.'-'.$day;		
		$age = floor((time()-strtotime($birthdate))/(60*60*24*365));                   
		if($age < 18 || $age>30){
			return false;
		}else{
			return $birthdate;
		}
	}
	
	public function dataCandidate( $candidate ){						
		
		// $validateCURP = $this->validateCURP($candidate);
		// if($validateCURP !== true){	
  //           return array('errorCURP' => true);
		// }

		// $candidate['birthdate'] = $this->getBirthdate($candidate['curp']);
		// if($candidate['birthdate'] === false){
		// 	return array('errorCURP' => true);
		// }
		
		$fields = array(
			'name',
			'last_name',
			'email',
			'zip_code',
			'address',
			'birthdate',
			'gender',
			'phone_number',
			'cell_number',
			'id_city',
			'id_state',
			'id_country'	
		);
		
		$rows = array();
		
		foreach($fields as $field){
			if(isset($candidate[$field])){
				$rows[] = "
				".$field." = '".$candidate[$field]."'";
			}
		}		
		
		if(!empty($rows) && !empty($candidate['uid'])){
			$sql = "
			UPDATE candidates 
			SET ".implode(", ", $rows)." 
			WHERE uid = ".$candidate['uid']." ";
                        //error_log("[ ".date("Y-m-d H:i:s")." ] - Candidate( ".$candidate['uid']." ) - ".$sql." \n",3,"/home/web/projects/logs/gkm-core/registration.log");
			return Database::executeConn($sql,"pilares");
		}else{
			return false;
		}
		
	}
	
	public function getCandidateUid( $token ){
		
		$sql = "
		SELECT * 
		FROM us_users U
		INNER JOIN us_users_passwords P ON U.uid = P.uid
		WHERE token = '".$token."'
		";
		
		$pdo = Database::execute($sql);
		if($pdo !== false){
			return Database::fetch_row($pdo);
		}
		
	}
	
	public function getCandidateToken( $uid ){
		
		$sql = "
		SELECT token 
		FROM us_users
		WHERE uid = '".$uid."'
		AND token != ''
		AND token IS NOT NULL
		";
		
		$pdo = Database::execute($sql);
		if($pdo !== false){
			return Database::fetch_row($pdo);			
		}
	}
	
	public function activateCandidate( $uid ){
		$sql = "
		UPDATE us_users
		SET id_status = ".self::ACTIVE_CANDIDATE.",
		token = ''
		WHERE uid = ".$uid."
		";
		
		return Database::execute($sql);		
	}
	
	public function createCompany( $company, $user ){
		
		$data = $this->getCandidateUid( $user['token'] );
		if(!empty($data['uid']) && empty($data['id_status'])){			
			$user['uid'] = $data['uid'];
			$user['name'] = $data['name'];
			$user['last_name'] = $data['last_name'];
			$user['email'] = $data['email'];
		}else{
			return false;
		}
		
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
			'director_birthdate'
		);
		
		$rows = array();
		
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
				$user['id_company'] = $data['id'];
				$pdo = $this->dataUser($user);
				if($pdo !== false){
					$sql = "
					UPDATE us_users
					SET token = ''
					WHERE uid = ".$user['uid']."
					";
					return Database::execute($sql);				
				}else{
					return false;
				}
			}		
		}
	}
	
	public function getCompanyId( $token ){
		
		$sql = "
		SELECT * 
		FROM companies C		
		WHERE token = '".$token."'
		";
		
		$pdo = Database::executeConn($sql,"pilares");
		if($pdo !== false){
			return Database::fetch_row($pdo);
		}
	}
	
	public function addCompanyUser( $user )	{
		
		foreach ($user as $key => $value) {
            if (empty($value)) {
                return false;
            }
        }
		
		$user['rol'] = self::ROL_COMPANY;
		$uid = Users::add($user);
		if(is_numeric($uid)){			
			return $uid;			
		}else{
			return false;
		}		
		
	}
	
	public function dataUser( $user ){
		
		$fields = array(
			'uid',
			'name',
			'last_name',
			'email',
			'birthdate',
			'gender',
			'phone_number',
			'cell_number',
			'id_company'
		);
		
		$rows = array();
		
		foreach($fields as $field){
			if(!empty($user[$field])){
				$rows[$field] = $field; 
				$values[$field] = "'".$user[$field]."'";
			}
		}
		if(!empty($rows)){
			$sql = "INSERT INTO users_companies(".implode(', ',$rows).", created) VALUES(".implode(', ', $values).", NOW())";						
			return Database::executeConn($sql,"pilares");			
		}
		
	}
        
        
        function getCURP( $uid ){
            $sql = "
            SELECT curp
            FROM candidates
            WHERE uid = ".$uid."
            ";
            
            $pdo = Database::executeConn($sql,"pilares");
            if($pdo !== false){
                return Database::fetch_row($pdo);
            }
        }
	
}

?>