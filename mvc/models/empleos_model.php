<?php 
class empleos_model{
	
	function searches( $uid ){
		
		$searches = array();
		
		$sql = "
		SELECT name
		FROM users_companies UC
		INNER JOIN vacancies V ON V.id_company = UC.id_company
		WHERE UC.uid = ".$uid."
		";
		
		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_array($pdo)){
			$searches[] = $row;	
		}
		
		return $searches;
		
	}
	
}
?>