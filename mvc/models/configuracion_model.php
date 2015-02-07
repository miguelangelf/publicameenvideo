<?php 

class configuracion_model{
	
	function changePassword($current, $new, $uid){				
		return Users::changePassword($current, $new, $uid);						
	}

	public function addSchool($candidate_id = null, $school = null) {
		if(!empty($candidate_id)) {
			$sql = "INSERT INTO `schools`
			(
			`name`,
			`degree`,
			`startYear`,
			`endYear`,
			`candidate_id`
			)
			VALUES('".$school["name"]."', '".$school["degree"]."', '".$school["startYear"]."', '".$school["endYear"]."', ".$candidate_id.");";

			$conn = Database::getExternalConnection("pilares");
			$pdo = $conn->query($sql);
			$id = $conn->lastInsertId();

			if(!empty($id)) return $id;
			else return 0;
		}
	}

	function addJob($candidate_id = null, $job = null) {

		if(is_null($job["endMonth"]) || is_null($job["endYear"])) $endDate = null;
		else $endDate = $job["endMonth"]."-".$job["endYear"];

		$startDate = $job["startMonth"]."-".$job["startYear"];
		$description = Database::real_escape($job["job_description"]);

		$sql = "INSERT INTO `jobs`
		(
		`position`,
		`company`,
		`job_description`,
		`senior_level`,
		`size`,
		`startDate`,
		`endDate`,
		`candidate_id`,
		`id_country`,
		`id_city`,
		`current`,
		`industry`
		)
		VALUES('".$job["position"]."', '".$job["company"]."', '".$description."', '".$job["senior_level"]."', '1-10', '".$startDate."', '".$endDate."', ".$candidate_id.", '".$job["id_country"]."', '".$job["id_city"]."', ".$job["current"].", '".$job["industry"]."');";

		$conn = Database::getExternalConnection("pilares");
		$pdo = $conn->query($sql);
		$id = $conn->lastInsertId();

		if(!empty($id)) return $id;
		else return 0;
	}

	public function updateJob($job = null) {

		if(is_null($job["endMonth"]) || is_null($job["endYear"])) $endDate = null;
		else $endDate = $job["endMonth"]."-".$job["endYear"];

		$startDate = $job["startMonth"]."-".$job["startYear"];
		$description = Database::real_escape($job["job_description"]);

		$sql = "UPDATE jobs SET position = '".$job["position"]."', company = '".$job["company"]."', job_description = '".$description."', senior_level = '".$job["senior_level"]."', size = '1-10', startDate = '".$startDate."', endDate = '".$endDate."', id_country = '".$job["id_country"]."', id_city = '".$job["id_city"]."', current = ".$job["current"].", industry = '".$job["industry"]."' WHERE id = ".$job["id_job"];
		
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		return $success;

	}

	public function updateSchool($school = null) {

		$sql = "UPDATE schools SET name = '".$school["name"]."', degree = '".$school["degree"]."', startYear = '".$school["startYear"]."', endYear = '".$school["endYear"]."' WHERE id = ".$school["id_school"];
		
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		return $success;

	}

	public function deleteJob($id = null) {
		
		$sql = "DELETE FROM jobs WHERE id = $id";
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		if($success == 0){
			return true;
		}else{
			return false;	
		}
	}

	public function deleteSchool($id = null) {
		
		$sql = "DELETE FROM schools WHERE id = $id";
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		if($success == 0){
			return true;
		}else{
			return false;	
		}
	}

	public function selectJobs($id = null) {

		$jobs = array();
		$sql = "SELECT * FROM jobs WHERE candidate_id = $id ORDER BY SUBSTRING_INDEX(SUBSTRING_INDEX(startDate, '-', 2), '-', -1) DESC";

		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_array($pdo)){	
			$jobs[] = $row;
		}

		return $jobs;
	}

	public function editJob($id = null) {

		$jobs = array();
		$sql = "SELECT * FROM jobs WHERE id = $id";

		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_assoc($pdo)){	
			$jobs[] = $row;
		}

		return $jobs;
	}

	public function editSchool($id = null) {

		$jobs = array();
		$sql = "SELECT * FROM schools WHERE id = $id";

		$pdo = Database::executeConn($sql, "pilares");
		while($row = Database::fetch_assoc($pdo)){	
			$jobs[] = $row;
		}

		return $jobs;
	}

	public function selectSchools($id = null) {
		$schools = array();
		if(!empty($id)) {
			$sql = "SELECT * FROM schools WHERE candidate_id = $id ORDER BY startYear DESC";
			
			$conn = Database::getExternalConnection("pilares");
			$pdo = $conn->query($sql);

			while($row = Database::fetch_assoc($pdo)) {
				$schools[] = $row;
			}
			return $schools;
		}
	}

	function addCurriculum($curriculum = null, $candidate_id = null, $extension = null) {
		
		$sql = "INSERT INTO `curriculums` ( `candidate_id`, `description`, `name`, `extension`) 
				VALUES ( $candidate_id, '".$curriculum["description"]."', '".$curriculum["name"]."', '".$extension."' );";

		$conn = Database::getExternalConnection("pilares");
		$pdo = $conn->query($sql);
		$id = $conn->lastInsertId();

		return $id;
		
	}

	public function deleteCurriculum($id = null) {
		$sql = "DELETE FROM curriculums WHERE id = $id";
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		if($success == 0){
			return true;
		}else{
			return false;	
		}
	}

	function updateDescription($id, $curriculum = null, $extension = null) {
		$sql = "";
		if(!empty($curriculum["description"])) $sql .= "UPDATE curriculums SET description = '".$curriculum["description"]."' WHERE id = $id;";
		if(!empty($curriculum["name"])) $sql .= "UPDATE curriculums SET name = '".$curriculum["name"]."', extension = '".$extension."' WHERE id = $id;";

		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		return $success;
	}

	function getCurriculums($uid) {
		$sql = "SELECT * FROM curriculums WHERE candidate_id = $uid";
		$conn = Database::getExternalConnection("pilares");
		$pdo = $conn->query($sql);

		$curriculums = array();

		while($row = Database::fetch_assoc($pdo)) {
			$curriculums[] = $row;
		}
		return $curriculums;
	}

	public function saveSL($skills = null, $languages = null, $candidate_id) {
		
		$languages = str_replace("\\", "\\\\", $languages);
		
		if(!empty($skills)) $sql = "UPDATE candidates SET skills = '".$skills."' WHERE uid = $candidate_id;";
		if(!empty($languages)) $sql = "UPDATE candidates SET languages = '".$languages."' WHERE uid = $candidate_id;";
		
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		return $success;
	}

	public function getSL($type = null, $candidate_id) {
		$sql = "SELECT ".$type." FROM candidates WHERE uid = $candidate_id LIMIT 1";

		$conn = Database::getExternalConnection("pilares");
		$pdo = $conn->query($sql);

		$row = Database::fetch_assoc($pdo);
		
		return $row[$type];
	}

	public function findCountries(){

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

	public function findStates( $country_iso_code = 'MX'){
		$records = array();
		$sql = "
		SELECT
			geoname_id as geoname_id,
			subdivision_1_name
		FROM states S
		WHERE locale_code = 'es' 		
		AND subdivision_1_name != ''
		AND country_iso_code = '".$country_iso_code."'
		ORDER BY subdivision_1_name
		";
		$pdo = Database::executeConn($sql, 'geo');
		while($row = Database::fetch_array($pdo)){			
			$records[$row['geoname_id']] = $row['subdivision_1_name'];
		}

		return $records;	
	}


	public function findCities( $id_country, $id_state ){
		$records = array();
		$sql = "
		SELECT
			geoname_id,
			city_name
		FROM cities C
		WHERE locale_code = 'es' 				
		AND country_iso_code = '".$id_country."'
		AND subdivision_1_name = '".$id_state."'
		AND city_name != ''
		ORDER BY city_name
		";
		$pdo = Database::executeConn($sql, 'geo');
		while($row = Database::fetch_array($pdo)){			
			$records[$row['geoname_id']] = utf8_encode($row['city_name']);
		}

		return $records;	
	}

	public function findCityByCountry($id_country) {
		$records = array();
		$sql = "
		SELECT
			geoname_id,
			city_name
		FROM cities C
		WHERE locale_code = 'es' 				
		AND country_iso_code = '".$id_country."'
		AND city_name != ''
		ORDER BY city_name
		";
		$pdo = Database::executeConn($sql, 'geo');
		while($row = Database::fetch_array($pdo)){			
			$records[] = array( $row['geoname_id'] => utf8_encode($row['city_name']));
		}

		return $records;
	}

	public function findCityCountry($id) {
		$records = array();
		if(empty($id)) return null;
		$sql = "
		SELECT
			country_name,
			city_name
		FROM cities
		WHERE geoname_id = $id AND locale_code = 'es'";

		$pdo = Database::executeConn($sql, 'geo');
		while($row = Database::fetch_array($pdo)){	
			$records[] = array( 'country' => utf8_encode($row['country_name']), 'city' => utf8_encode($row["city_name"]));
		}

		return $records;
	}

	// function editJob($id = null) {
	// 	$sql = "SELECT * FROM jobs WHERE id = $id";
	// 	$conn = Database::getExternalConnection("pilares");
	// 	$pdo = $conn->query($sql);

	// 	// while($row = Database::fetch_array($pdo)){
	// 	// 	$row
	// 	// }
	// }
	
}

?>