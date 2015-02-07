<?php

class _Messages {

	public static function addMessage($uid_sender, $uid_to, $subject = null, $body = null, $date) {

		$body = addslashes($body);
		$sql = "INSERT INTO `messages`
		(
		`sender`,
		`receiver`,
		`subject`,
		`body`,
		`date`,
		`flag`
		)
		VALUES ($uid_sender, $uid_to, '".$subject."', '".$body."', '".$date."', 0);";

		$conn = Database::getExternalConnection("pilares");
		$pdo = $conn->query($sql);
		$id = $conn->lastInsertId();

		if(!empty($id)) return $id;
		else return 0;
	}

	public static function getMessages($uid, $receiver, $type = 'inbox') {

		if($type == "inbox") {
			$sql = "SELECT m.subject as subject, m.body as body, m.date as date, IF(c.name IS NULL, 
					(SELECT CONCAT(c.name, '|', u.name)
					FROM users_companies u 
						INNER JOIN companies c ON c.id = u.id_company 
					WHERE u.uid = uc.uid), c.name) AS envia, m.flag as leido, m.id as id FROM messages m
					LEFT JOIN candidates c ON c.uid = m.sender
					LEFT JOIN users_companies uc ON uc.uid = m.sender
				WHERE m.receiver = $uid ORDER BY m.date DESC";
		} else {
			$sql = "SELECT m.subject AS subject, m.body AS body, m.date AS date, IF(c.name IS NULL,
						(SELECT CONCAT(c.name, '|', u.name)
						FROM users_companies u
							INNER JOIN companies c ON c.id = u.id_company 
						WHERE u.uid = uc.uid), c.name) AS recibe FROM messages m
						LEFT JOIN candidates c ON c.uid = m.receiver
						LEFT JOIN users_companies uc ON uc.uid = m.receiver
					WHERE m.sender = $uid ORDER BY m.date DESC";
		}

        $pdo = Database::executeConn($sql,"pilares");
        $messages = array();
        $messages["type"] = $type;
        while($row = Database::fetch_assoc($pdo)){
        	
        	$aux = preg_match("/width[:]?\s*\d*px/", $row["body"], $matches);
        	if(!empty($matches)) $row["body"] = str_replace($matches[0], "width: 500px", $row["body"]);
        	if($type == 'inbox') {
        		$row["recibe"] = $receiver;
	        	$sender = explode("|", $row["envia"]);
	        	if(count($sender) > 1) {
	        		$row["envia"] = $sender[1];
	        		$row["company"] = $sender[0];
	        	} else {
	        		$row["envia"] = $sender[0];
	        	}
        	} else {
        		$row["envia"] = $receiver;
	        	$sender = explode("|", $row["recibe"]);
	        	if(count($sender) > 1) {
	        		$row["recibe"] = $sender[1];
	        		$row["company"] = $sender[0];
	        	} else {
	        		$row["recibe"] = $sender[0];
	        	}
        	}

        	$datediff = null;
        	$today = null;
        	$message_date = null;
        	$row["time_diff"] = "";

        	$today = date_create(date('Y-m-d H:i:s'));
        	$message_date = date_create($row["date"]);
        	$datediff = date_diff($today, $message_date);

        	foreach ($datediff as $key => $value) {
        		if($value > 0) {
        			switch ($key) {
        				case 'd':
        					$row["time_diff"] = ($value > 1) ? $message_date->format("d/m/Y") : "Ayer";
        					break;
        				case 'h':
        					$row["time_diff"] = ($value > 1) ? "Hace ".$value." horas" : "Hace ".$value." hora";
        					break;
        				case 'i':
        					$row["time_diff"] = ($value > 1) ? "Hace ".$value." minutos" : "Hace unos minutos";
        					break;
        				case 's':
        					$row["time_diff"] = "Hace unos momentos";
        					break;
        				default:
        					$row["time_diff"] = $message_date->format("d/m/Y");
        					break;
        			}
        			break;
        		}
        	}


        	$row["body"] = utf8_encode($row["body"]);
        	// print_r($row["body"]);
            $messages["messages"][] = $row;
            $messages["javascript"][] = json_encode($row);
        }

        return $messages;

	}

	public static function updateFlag($id) {
		$sql = "UPDATE messages SET flag = 1 WHERE id = $id;";

		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		return $success;
	}

	public static function deleteMessage($id) {

		$sql = "DELETE FROM messages WHERE id = $id";
		$success = (Database::executeConn($sql, "pilares") !== false) ? 0 : 1;

		if($success == 0){
			return true;
		}else{
			return false;	
		}
	}

}

?>