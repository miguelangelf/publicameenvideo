<?php

class scheduler_model {
	
	
	const RESCHEDULE_MEETING = 4;	
    
    public function getAvailables($office_id, $slots){               
        $sql = "CALL availableSlots(".$office_id.",".$slots.")";
        $pdo = Database::executeConn($sql,"pilares");
        $num = Database::num_rows($pdo);
        $resp = array();
        if($num > 0){
            while($row = Database::fetch_array($pdo)){
                $resp[] = $row;
            }
            return $resp;
        }else{
            return null;
        }
    }
    
    public function schedule($date,$hour,$officeID,$candidateID,$type){
        $sql = "CALL scheduleSlot('".$date."','".$hour."',".$officeID.",".$candidateID.",'".$type."')";
        $pdo = Database::executeConn($sql,"pilares");
        $num = Database::num_rows($pdo);
        if($num > 0){
            return Database::fetch_row($pdo);
        }else{
            return null;
        }
    }
	
    public function accessToSchedule( $uid ){
        $_sql = "SELECT exam_finished FROM candidates WHERE uid=$uid LIMIT 1";
        $_pdo = Database::executeConn($_sql,"pilares");
        $_num = Database::num_rows($_pdo);
        if($_num > 0){
            $_row = Database::fetch_row($_pdo);
            if($_row["exam_finished"] == 1){
                return false;
            }else{
				
                $sql = "
                SELECT *
                FROM interviews_offices I
                INNER JOIN calendar	C ON I.id = C.id_table
				LEFT JOIN notification_logs N ON N.id_notification_type = ".self::RESCHEDULE_MEETING."
				AND N.id_candidate = I.id_candidate
				AND N.notification_date = C.date
                WHERE I.id_candidate = ".$uid."
                AND C.date >= CURDATE( )		
				ORDER BY C.date DESC
				LIMIT 1
                ";
                $pdo = Database::executeConn($sql,"pilares");
                $num = Database::num_rows($pdo);
				$row = Database::fetch_row($pdo);

                if($num > 0){
					if(!empty($row['notification_date'])){					
                    	return true;
					}else{
						return false;
					}
                }else{            
                    return true;
                }
            }
        }else{
            return false;
        }        
    }
    
    public function getSchedule($uid,$type){
        $sql = "SELECT OTS.id_office,IO.id_candidate,C.date, TS.time
                FROM calendar C, interviews_offices IO, office_to_time_slots OTS, time_slots TS 
                WHERE IO.type = '$type' AND C.id_table=IO.id AND IO.id_office_to_time_slot = OTS.id AND OTS.id_time_slot = TS.id
                AND IO.id_candidate=$uid ORDER BY 3 DESC, 4 DESC LIMIT 1";
        $pdo = Database::executeConn($sql,"pilares");
        $num = Database::num_rows($pdo);
        if($num > 0){
            return Database::fetch_row($pdo);
        }else{
            return null;
        }
    }
    
}
