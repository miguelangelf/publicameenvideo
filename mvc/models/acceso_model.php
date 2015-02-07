<?php
/**
* 
*/
class acceso_model
{
	
	function __construct()
	{
		# code...
	}

	public function getEmailByCurp( $curp ){

		$sql = "
		SELECT uid 
		FROM candidates
		WHERE curp = '".Database::sanitizer($curp)."'
		";

		$pdo = Database::executeConn($sql, "pilares");
		$row = Database::fetch_row($pdo);

		if(!empty($row['uid'])){
			
			$sql = "
			SELECT email
			FROM us_users 
			WHERE uid = ".$row['uid']."
			";

			$pdo = Database::execute($sql);
			$row = Database::fetch_row($pdo);

			return !empty($row['email']) ? $row['email'] : false;

		}else{
			return false;
		}

	}
	
	function getUserByEmail( $email ){
		
		$sql = "
		SELECT *
		FROM us_users 
		WHERE email = '".$email."'
		";

		$pdo = Database::execute($sql);
		$row = Database::fetch_row($pdo);

		return !empty($row['uid']) ? $row : false;	
	}
	
	function setToken( $uid, $token ){
		$sql = "
		UPDATE us_users
		SET token = '".$token."'
		WHERE uid = ".$uid."
		";
		
		return Database::execute($sql);	
	}
	
}

?>