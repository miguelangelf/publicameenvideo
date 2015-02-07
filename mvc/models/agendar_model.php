<?php

class agendar_model{
	
	const PERMITION_MINORS = 5;
	const CARD_VOTER = 3;
	const NO_SHOW = 1;
	const LACK_OF_DOCUMENTS = 2;
	const CANDIDATE_ACCEPTED = 3;
	const RESCHEDULE_MEETING = 4;
	const TEST_UNCOMPLETED = 5;
	const TEST_FINALISED = 6;
	const CONFIRM_IDENTITY = 7; 
    
    public function getCalendarData($year, $month, $company_id){
        $sql = "
		SELECT DATE(date_time) as date
        FROM interviews_candidates I
        WHERE I.id_company = ".$company_id." 
        AND YEAR(date_time) = '".$year."'
        AND MONTH(date_time) = '".$month."'
		GROUP BY DATE(date_time)
		";
        $pdo = Database::executeConn($sql, "pilares");
        $r = array();
        while($row = Database::fetch_array($pdo)){
            $r[] = $row['date'];
        }
        return ($r);
    }
    
    public function getTimeSlots(){
        $sql = "SELECT time FROM time_slots ORDER BY weight";
        $pdo = Database::executeConn($sql, "pilares");
        $r = array();
        while($row = Database::fetch_array($pdo)){
            $r[] = $row['time'];
        }
        return ($r);
    }
    
    public function getSchedule($date, $company_id){
       $sql = "SELECT DATE(date_time) AS date, TIME(date_time) AS time, id_candidate, curp, birthdate, phone_number, 0 AS exam_finished, 0 AS disabled
                FROM candidates C
                INNER JOIN interviews_candidates I ON C.uid = I.id_candidate								
                WHERE DATE(I.date_time) = '".$date."' 
				AND I.id_company = ".$company_id." 				
                ORDER BY TIME(date_time)";
        $pdo = Database::executeConn($sql, "pilares");
        $rows = array();
        $cnt = 0;
        while($rsRow = Database::fetch_assoc($pdo)){
            $rows[$cnt]["schedule"] = $rsRow;
            $rows[$cnt]["candidate"] = agendar_model::getCandidateData($rsRow['id_candidate']);
            $cnt++;
        }
        return $rows;
    }
    
    public function getCandidateData($uid){
        $sql = "SELECT name, last_name, email FROM candidates WHERE uid = ".$uid;
        $pdo = Database::executeConn($sql, "pilares");
        $rsRow = Database::fetch_assoc($pdo);
        return $rsRow;
    }
	
	public function registerDocument( $document ){
		if($document['action'] == 'true'){
			$sql = "INSERT INTO documents_candidates(uid, id_document, sy_file_id, created) VALUES(".$document['id_candidate'].", ".$document['type'].", 0, NOW())";
			$conn = Database::getExternalConnection("pilares");
			$pdo = $conn->query($sql);
			$pdo2 = $conn->query("SELECT LAST_INSERT_ID() as id");
			$data =  Database::fetch_row($pdo2);
			unset($conn);		
			return $data['id'];
		}else{			
			return $this->deleteDocuments($document['id_candidate'], $document['type']);						
		}		
	}
	
	public function DocumentsCatalog( $teen = false){
		
		$docs = array();
		
		$sql = "
		SELECT * 
		FROM documents 
		";
		
		$sql .= !$teen ? "WHERE id != ".self::PERMITION_MINORS." " : "WHERE id != ".self::CARD_VOTER." ";
		
        $pdo = Database::executeConn($sql, "pilares");
        while($row = Database::fetch_assoc($pdo)){
			$docs[]	= $row;
		}
        return $docs;	
	}
	
	public function setFidDocument( $data ){
		
		$sql = "
		INSERT INTO `documents_candidates_company`
		(
			`fid`,
			`document_name`,
			`id_company`,
			`id_candidate`
		)
		VALUES
		(
			'".$data['fid']."',
			'".$data['document_name']."',
			'".$data['id_company']."',
			'".$data['id_candidate']."'
		)
		";
		
		return Database::executeConn($sql, "pilares");
		
	}
	
	public function setNotification( $id_candidate, $notification_type, $uid, $notification_date ){
		
		$sql = "
		INSERT INTO notification_logs(
			`uid`, 
			`id_notification_type`, 
			`id_candidate`, 
			`notification_date`,
			`created`			 
		) VALUES(
			".$uid.",
			".$notification_type.",
			".$id_candidate.",
			'".$notification_date."',
			NOW()	
		)				
		";
		
		return Database::executeConn($sql, "pilares");
		
	}
	
	public function accessExam( $cid, $date ){
            
            // STATUS:
            // 1 .- No esta en examen
            // 2 .- Ya se envio a examen
            // 3 .- Ya se envio a examen y ya se termino el examen          
            
            // Determinar si ya se envio al examen
            $sql = "SELECT COUNT(*) as with_exam FROM notification_logs N INNER JOIN candidates C ON C.uid = N.id_candidate WHERE id_notification_type = ".self::CANDIDATE_ACCEPTED." AND id_candidate = ".$cid." AND notification_date = '".$date."'";
            $pdo = Database::executeConn($sql,"pilares");
            $dat = Database::fetch_row($pdo);
            if($dat["with_exam"] == 0){
                $SENT_EXAM = FALSE;
            }else{
                $SENT_EXAM = TRUE;
            }
            
            // Determinar si terminaron el examen
            $sql = "SELECT exam_finished FROM candidates WHERE uid = $cid;";
            $pdo = Database::executeConn($sql,"pilares");
            $dat = Database::fetch_row($pdo);
            if($dat["exam_finished"] == 0){
                $EXAM_FINISHED = FALSE;
            }else{
                $EXAM_FINISHED = TRUE;
            }                    
			
			if(!$SENT_EXAM){
				return 1;
			}else{
				if($EXAM_FINISHED){
					return 3;
				}else{
					return 2;
				}
			}
				
	}


	public function deleteDocumentCompany( $id ){
								
		$sql = "
		SELECT * 
		FROM documents_candidates_company 
		WHERE id = ".$id." 		
		";		
		
		$pdoDC = Database::executeConn($sql, "pilares");
		$data = Database::fetch_array($pdoDC);
		
			$sql = "
			DELETE FROM documents_candidates_company 
			WHERE id = ".$data['id']." 
			";		
			$pdo = Database::executeConn($sql, "pilares");
			if($pdo !== false){
				if(!empty($data['fid'])){
					
					$sql = "
					SELECT * 
					FROM sy_files F
					INNER JOIN sy_filecategories C ON C.fcid = F.fcid					
					WHERE F.fid = ".$data['fid']."
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
			}else{
				return false;					
			}
		
		return true;	
	}
	
	
	public function deleteDocuments( $id_candidate, $type = false ){
								
		$sql = "
		SELECT * 
		FROM documents_candidates 
		WHERE uid = ".$id_candidate." 		
		";
		
		$sql .= ($type != false) ? " AND id_document = ".$type." " : '';
		
		$pdoDC = Database::executeConn($sql, "pilares");
		while($data = Database::fetch_array($pdoDC)){				
		
			$sql = "
			DELETE FROM documents_candidates 
			WHERE id = ".$data['id']." 
			";		
			$pdo = Database::executeConn($sql, "pilares");
			if($pdo !== false){
				if(!empty($data['sy_file_id'])){
					
					$sql = "
					SELECT * 
					FROM sy_files F
					INNER JOIN sy_filecategories C ON C.fcid = F.fcid
					WHERE F.fid = ".$data['sy_file_id']."
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
			}else{
				return false;					
			}
		}
		return true;	
	}
                
		
	function identityConfirmed( $id_candidate, $officeType ){
		$sql = "
		SELECT  id_candidate  
		FROM notification_logs
		WHERE id_candidate = ".$id_candidate."
		AND id_notification_type = ".self::CONFIRM_IDENTITY."
		";
		
		$pdo = Database::executeConn($sql,"pilares");
		$row = Database::fetch_row($pdo);
		return !empty($row) ? 1 : 0;
	}
	
}
?>