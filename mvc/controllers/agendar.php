<?php
class agendar extends _controller{
	
	const NO_SHOW = 1;
	const LACK_OF_DOCUMENTS = 2;
	const CANDIDATE_ACCEPTED = 3;
	const RESCHEDULE_MEETING = 4;
	const TEST_UNCOMPLETED = 5;
	const TEST_FINALISED = 6;
	const CONFIRM_IDENTITY = 7; 
    
    public function index(){		
		
		$user = $this->verifyAccess();						

        $company_id = _Company::getCompanyID($user['uid']);		
        
        $data = array();
        //  let's force the calendar label weekdays
        $weekdays = array("Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab");
        
        $model = $this->model();
        //  let's set the current day
        $data["currentDay"] = date("d");
        $data["currentWeekDay"] = date("l");
        $data["currentMonth"] = date("m");
        switch($data["currentMonth"]){
                case 1: $data["currentMonth"] = "Enero"; break;
                case 2: $data["currentMonth"] = "Febrero"; break;
                case 3: $data["currentMonth"] = "Marzo"; break;
                case 4: $data["currentMonth"] = "Abril"; break;
                case 5: $data["currentMonth"] = "Mayo"; break;
                case 6: $data["currentMonth"] = "Junio"; break;
                case 7: $data["currentMonth"] = "Julio"; break;
                case 8: $data["currentMonth"] = "Agosto"; break;
                case 9: $data["currentMonth"] = "Septimebre"; break;
                case 10: $data["currentMonth"] = "Octubre"; break;
                case 11: $data["currentMonth"] = "Noviembre"; break;
                case 12: $data["currentMonth"] = "Diciembre"; break;
         }
        $data["currentYear"] = date("Y");
        
        //  let's get the selected day if any
        $data["selectedDay"] = $this->Get(1);
        $getSelectedMonth = $this->Get(2);
        if(strlen($getSelectedMonth)==1)
            $getSelectedMonth = "0".$getSelectedMonth;
        $data["getSelectedMonth"] = $getSelectedMonth;
        $data["selectedYear"] = $this->Get(3);
        
        if(isset($data["selectedDay"]) && isset($data["getSelectedMonth"]) && isset($data["selectedYear"])){
            $data["selectedDay"] = $this->Get(1);
            if(strlen($data["selectedDay"])==1)
                $data["selectedDay"] = "0".$data["selectedDay"];
            //$data["selectedMonth"] = date("F", mktime(0, 0, 0, $data["getSelectedMonth"]));
            switch($getSelectedMonth){
                case "01": $data["selectedMonth"] = "Enero"; break;
                case "02": $data["selectedMonth"] = "Febrero"; break;
                case "03": $data["selectedMonth"] = "Marzo"; break;
                case "04": $data["selectedMonth"] = "Abril"; break;
                case "05": $data["selectedMonth"] = "Mayo"; break;
                case "06": $data["selectedMonth"] = "Junio"; break;
                case "07": $data["selectedMonth"] = "Julio"; break;
                case "08": $data["selectedMonth"] = "Agosto"; break;
                case "09": $data["selectedMonth"] = "Septimebre"; break;
                case "10": $data["selectedMonth"] = "Octubre"; break;
                case "11": $data["selectedMonth"] = "Noviembre"; break;
                case "12": $data["selectedMonth"] = "Diciembre"; break;
            }
            $data["selectedYear"] = $this->Get(3);
            //  let's extract the fatch data for the calendar on the selected day
            $fetchdata = $model->getCalendarData($data["selectedYear"], $data["getSelectedMonth"], $company_id);
            //$fetchdata = array();
            $data["calendar"] = Utils::calendarRender($data["getSelectedMonth"],$data["selectedYear"], $weekdays, $fetchdata);
            $data["currentNumMonth"] = $data["getSelectedMonth"];
        }else{
            $data["currentNumMonth"] = date("m");
            //  let's extract the fatch data for the current month
            $fetchdata = $model->getCalendarData($data["currentYear"], $data["currentNumMonth"], $company_id);
            //$fetchdata = array();
            $data["calendar"] = Utils::calendarRender($data["currentNumMonth"],$data["currentYear"], $weekdays, $fetchdata);
        }
        
        $fd = $this->Get(1);
        if(!empty($fd)){
            $data["fd"] = 1;
        }else{
            $data["fd"] = 0;
        }
        
        //$data["timeSlots"] = $model->getTimeSlots();		
        $data['roles'] = $user['roles'][0];
        $data['agenda'] = 'open';                		
		//$data['office'] = _Offices::getOfficeData($office_id);

        $this->view("index", $data); 
    }
    
    public function getDailySchedule(){
        //$office_id = 3; //this ID must be taken from the login process
        $user = $this->verifyAccess();
        $company_id = _Company::getCompanyID($user['uid']);
        //$officeType = _Offices::getOfficeType($officeID);
        $date = $this->Post("fd");
        $model = $this->model();
        $data["rsSchedule"] = $model->getSchedule($date, $company_id);
        $data["selectedDate"] = $date;
        $data["currentDay"]["day"]        = date("d", strtotime($date));
        $data["currentDay"]["month"]      = _Catalogs::getMonth(date("m", strtotime($date)));
        $data["currentDay"]["year"]       = date("Y", strtotime($date));
        $data["currentDay"]["dayofweek"]  = _Catalogs::getDayOfWeek(date("w", strtotime($date)));
        
        $this->view("dailySchedule", $data); 
    }
    
    public function getScheduleDetail(){  
        
        $user = $this->verifyAccess();
        $company_id = _Company::getCompanyID($user['uid']);
        //$officeType = _Offices::getOfficeType($officeID);
        
        $candidateId = $this->Post("cid");
		$date = $this->Post("date");
        $data = $this->controller('candidatos')->candidato($candidateId, true);
		$model = $this->model();
		$teen = $data['candidate'][$candidateId]['age'] < 18 ? true : false;
		$documents = $model->DocumentsCatalog( $teen );				
		$candidate_documents = array();		
		if(!empty($data['candidate'][$candidateId]['documents'])){
			foreach($data['candidate'][$candidateId]['documents'] as $document){
				$candidate_documents[$document['id_document']] = $document;
			}
		}
		
		//print_r($data['candidate'][$candidateId]);
		//$data['candidate'][$candidateId]['with_exam'] = $model->accessExam($candidateId, $date);
		//$data['candidate'][$candidateId]['identity'] = $model->identityConfirmed($candidateId, $officeType);				
			
        $this->view(
			"scheduleDetail", 
			array(
				'candidate' => $data['candidate'][$candidateId]
			)
		); 
    }
	
	public function registerDocument(){
		
		$document = POST::get('document');
		$adminDocument = $this->model();		
		$result = $adminDocument->registerDocument( $document );
		if($result){
			$response['status'] = 1;
			if(is_numeric($result)){
				$response['id'] = $result;
			}
		}else{
			$response['status'] = 0;
			$response['document'] = $document;
		}
		echo json_encode($response);
	}
	
	public function uploadDocuments(){
		
		$user = $this->verifyAccess();	
	
		$uploadFiles = $this->Module("uploadFiles");
		
		$error_messages = array(
			1                           => '1. El tamaño del archivo debe ser menor a 1MB',
			2                           => '2. El tamaño del archivo debe ser menor a 1MB',
			3                           => '3. El archivo no termino de cargarse',
			4                           => '4. No se ha podido subir el archivo',
			6                           => '6. No se ha podido subir el archivo',
			7                           => '7. No se ha podido subir el archivo',
			8                           => '8. No se ha podido subir el archivo',
			'post_max_size'             => '9. El tamaño del archivo debe ser menor a 1MB',
			'max_file_size'             => '10. El tamaño del archivo debe ser menor a 1MB',
			'accept_file_types'         => '10. Los tipos de archivos permitidos son: *.pdf, *.doc, *.docx',			
			'abort'                     => '11. No se ha podido subir el archivo',
			'failure_make_folder'       => '12. No se ha podido subir el archivo',
			'failure_register_path'     => '13. No se ha podido subir el archivo',
			'failure_register_file'     => '14. No se ha podido subir el archivo'
		);
		
		$uploadFiles->setErrorMessages($error_messages);
									
		$response = $uploadFiles->upload(
			$_FILES, //FILES via POST
			$user['uid'],	//uid 
			Config::get('Theme.Web.uploads'), //Directory from uploads 
			'scan/pdfs/', //Constant of upload path  
			array(
				'pdf',
				'doc',
				'docx',
				'jpeg',
				'jpg',
				'bmp',
				'gif',
				'png'
			),	//Extensions allowed 
			'2M', //Max Size
			$uploadFiles::ENCRYPT_WITHOUT_EXT
		);				
		
		echo json_encode($response);		
		
	}
	
	public function setFidDocument(){
		
		$document_name = POST::get('document_name');
		$id_candidate = POST::get('id_candidate');
		$fid = POST::get('fid');
		$user = $this->verifyAccess();
		$id_company = _Company::getCompanyID($user['uid']);		
		
		$response['status'] = 0;
		
		if(!empty($fid) && !empty($document_name)){
			$adminDocument = $this->model();		
			$result = $adminDocument->setFidDocument( array(
				'fid' => $fid, 
				'document_name' => $document_name, 
				'id_company' => $id_company,
				'id_candidate' => $id_candidate
			));	
			if($result !== false){
				$response['status'] = 1;
			}	
		}
		
		echo json_encode($response);
		
	}
	
	public function viewDocument(){
		$file = $this->Get(1);
		if(!empty($file)){
			$file = $this->Module("uploadFiles")->viewFile(Config::get('Theme.Web.uploads'), $file);		
			if($file === false){
				echo "2. El documento no existe";
			}
		}else{
			echo "1. El documento no existe";
		}
	}
	
	function setFolio( $type, $id_candidate ){
		$data = $this->controller('candidatos')->candidato($id_candidate, true);
		$folio = $type.$data['candidate'][$id_candidate]['folio']; 
		$this->controller('registro')->updateCandidate(array('uid' => $id_candidate, 'folio' => $folio));
	}
	
	public function notification(){
		
		$user = $this->verifyAccess();
		
		$id_candidate = POST::get('id_candidate');
		$id_notification = POST::get('id_notification');
		$notification_date = POST::get('selected_date');
		$accessCode = _Offices::getOfficeAccessCode($user['uid']);
		$notification_type = 0;
		$response['status'] = 0;				
		
		if(is_numeric($id_candidate)){
			
			$adminNotification = $this->model();
			
			switch($id_notification){
				case self::CANDIDATE_ACCEPTED:
					$resp = $this->hiumanCall($id_candidate, $accessCode);
					if($resp["status"] == 0){
						$response['status']  = 0;
						$response['message'] = $resp["information"];
						echo json_encode($response);
						exit;
					}
					$notification_type = $id_notification;
				break;
				case self::NO_SHOW:
					$notification_type = $id_notification;
					$adminNotification->deleteDocuments($id_candidate);					
					$this->setFolio(self::NO_SHOW, $id_candidate);
				break;
				case self::CONFIRM_IDENTITY:
					$notification_type = $id_notification;
				break;				
				case self::TEST_UNCOMPLETED:
					$notification_type = $id_notification;
					$adminNotification->deleteDocuments($id_candidate);
					$this->setFolio(self::TEST_UNCOMPLETED, $id_candidate);
				break;
				case self::RESCHEDULE_MEETING:
					$notification_type = $id_notification;
					$adminNotification->deleteDocuments($id_candidate);
					$this->setFolio(self::RESCHEDULE_MEETING, $id_candidate);
				break;
				case self::LACK_OF_DOCUMENTS:
					$notification_type = $id_notification;
					$adminNotification->deleteDocuments($id_candidate);
					$this->setFolio(self::LACK_OF_DOCUMENTS, $id_candidate);
				break;
			}						
			
			if(!empty($notification_type)){
                            
                                $data = $this->controller('candidatos')->candidato($id_candidate, true);                                
                                $exam_finished = $data["candidate"][$id_candidate]["exam_finished"];
                                
                                if($exam_finished == 0){						
                                    $result = $adminNotification->setNotification( $id_candidate, $notification_type, $user['uid'], $notification_date );
                                    if($result !== false){
                                            $response['status'] = 1;
                                    }
                                }else{
                                    $response['status'] = 1;
                                }
                                
                                // Send Email when no-show notification is enable
                                if($notification_type == self::NO_SHOW){
                                    
                                    $email = $data["candidate"][$id_candidate]["email"];
									$name = $data['candidate'][$id_candidate]['name'].' '.$data['candidate'][$id_candidate]['last_name'];
                                    $body = utf8_decode(_twig('mails/no_show.twig', array('name' => $name)));					
                                    $subjet = 'Reagenda tu cita '.utf8_decode($name);
                                    $mail = new Mail("default", $subjet, utf8_decode($body));					
                                    $mail->addAddress($email);      				
                                    if($mail->send()){
					$response['email'] = "success";
                                    }else{		
                                        $response['email'] = "error";
                                    }
                                }
                                   
                                // --------------
			}
			
		}
		
		echo json_encode($response);
		
	}
        
        private function hiumanCall($candidateID, $accessCode){
            $API       = API::initialize("http://69.94.133.56/khorPilares");
            $candidate = $this->Controller("candidatos")->getCandidate($candidateID);
            $data     = array(
                "userName"   => $candidate["folio"],
                "LastName"   => $candidate["last_name"],
                "FirstName"  => $candidate["name"],
                "AccessCode" => $accessCode //"hrwek23skjhdf",
                );
            $response = $API->exec("srvPilares.asp",$data);
            $code   = $response["code"];
            $output = $response["output"];
            $header = $response["header"];
            $sql = "INSERT INTO api_log(code,created,response,header,sent) VALUES('$code',NOW(),'$output','$header','".json_encode($data)."')";   
            $status = 1;
            $information = "success";
            if(strpos($output,"errors") !== false){
                $status = 0;
                $information = "Error"; 
            }
            Database::executeConn($sql,"pilares");
            return array("status"=>$status,"information"=>$information);
        }

    function deleteDocumentCompany(){

    	$post = POST::isReady();		
		if($post){	
			$id = POST::get('id');
	    	$documentModel = $this->model();    	
	    	$result = $documentModel->deleteDocumentCompany( $id );

	    	$response['status'] = 0;
	    	if($result !== false){
				$response['status'] = 1;
			}

			echo json_encode($response);
		}
    }
	
}
?>