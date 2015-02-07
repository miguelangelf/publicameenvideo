<?php
date_default_timezone_set("America/Mexico_City");

class candidatos extends _controller{
    
    // Public views
    public function todos(){
        $user = $this->verifyAccess();
        $messages = _Messages::getMessages($user["uid"], $user["name"], "outbox");
        $data = array('roles' => $user['roles'][0], 'candidatos' => 'open');
        $companyID  = _Company::getCompanyID($user["uid"]);
        $this->Module("filters")->setOwner($companyID);
        $data["Filters"] = $this->getFiltersData($companyID);
        $data["Filters"]["Owner"] = $companyID;
        $data["messages"] = $messages;
        $this->view("todos", $data);	 
    }
    
    private function getFiltersData(){
        $params = array();
        $params["cities"] = $this->Model()->getCities();
        $params["studies"] = $this->Model()->getStudies();
        return $params;
    }
	
	public function filtered(){	
	
		$user = $this->verifyAccess();
		$params['rows'] = 10;
		$params['id_company'] = _Company::getCompanyID($user['uid']);
		$view = 'filtered';                                
		
		if($this->Get(1) == 'page'){
			$param = $this->Get(2);
			$params['page'] = (!empty($param) && is_numeric($param)) ? $param : 1;
		}				
		
		$type = POST::Get('type');
                
        if(empty($params['id_company'])){
            $this->view('no_candidates',array('type'=>$type));
            return false;
        }
        
        $search = $this->Post("search");
		$params["search"] = $search;
                
        $filters = $this->Post("filters");
		$params["filters"] = $filters;
                
		switch($type){
			case 'hiredCandidates':
				$params['status'] = 'hired';
			break;
			case 'inviteCandidates':
				$params['status'] = 'interview';
			break;
			case 'testCandidates':
				$params['status'] = 'test';
			break;			
		}
		
		$candidatesModel = $this->model();       
		
                $candidates = $candidatesModel->find( 
			false, 
			$params
		);
		
		if(empty($candidates)){				
			$this->view('no_candidates',array('type'=>$type));
		}else{		
			$pages =  Paginator::pagination(array(
				'total_rows' => $candidates['total_rows'], 
				'url' => Config::get("Core.Domains.core").'empresa/candidatos/filtered/',
				'current_page' => $params['page'],
				'page_rows' => $params['rows'],
				'max_pages' => 5,
				'next' => '>',
				'first' => '<<',			
				'last' => '>>',
				'previous' => '<',
				'info' => '%total_rows% Candidatos(s) en total'
				//'Page %page% of %pages%, showing %page_rows% records out of %total_rows% total, starting on record %start%, ending on %end%'			
				)
			);
			
			/*echo '<pre>';
			print_r($candidates);
			echo '</pre>';*/
			
			$this->view($view, array('tags'=>$candidates['tags'],'candidates' => $candidates['candidate'], 'pagination' => $pages, 'type' => $type));
		}
		
	}
        
    public function getCandidate($id){
        $candidate = $this->Model()->getCandidate($id);
        if(!is_null($candidate)){
            return $candidate;
        }else{
            return null;
        }
    }

    private function cmp($a, $b)
	{
	    return strcmp($a->skillLevel, $b->skillLevel);
	}
	
	public function candidato( $id_candidate = 0, $array = false, $jobs = null, $schools = null, $dataSL = null, $view = null, $curriculums = null){
		
		$uid = (empty($id_candidate)) ? POST :: get('candidate_id') : $id_candidate;
		$skills = array();
				
		$candidatesModel = $this->model();
		$data = $candidatesModel->find( $uid );

		$full_address = $candidatesModel->findCityState($data["candidate"][$uid]["id_city"]);
		if(!empty($full_address)) $data["candidate"][$uid]["full_address"] = $full_address[0]["city"].", ".$full_address[0]["state"];

		$profile_image = explode("|", $data["candidate"][$uid]["background_image"]);
		$file = null;
		$profilePicture = null;

		if(!empty($data["candidate"][$uid]["profile_picture"])) {
			$profilePicture = $data["candidate"][$uid]["profile_picture"];
			//$profilePicture = $this->Module("uploadFiles")->viewFileSource(Config::get('Theme.Web.uploads')."profile_pictures", $data["candidate"][$uid]["profile_picture"], false);
		}

		if(count($profile_image) > 1) {
			//$file = $this->Module("uploadFiles")->viewFileSource(Config::get('Theme.Web.uploads'), $profile_image[0], false);
			$file["name"] = $profile_image[0];
			$file["position"] = $profile_image[1];
		}

		if(!empty($dataSL["skills"])) {
			$skills = $dataSL["skills"];
			usort($skills, array($this, "cmp"));
		}

		if(!empty($view)) {
			$view_type = array("timeline", "classic");
			$key = array_search($view, $view_type);
			$view_result = $candidatesModel->updateLayout( $data["candidate"][$uid]["uid"], $key );
			$data["candidate"][$uid]["view"] = $key;
		}

		//$messages = null;
		$messages = _Messages::getMessages($uid, $data["candidate"][$uid]["name"], "inbox");
		// print_r($messages["javascript"][0]);
		//print_r($messages);

		$vars = array(
			'candidate' => $data['candidate'][$uid],
			'view' => ($id_candidate > 0) ? false : true,
			'jobs' => $jobs,
			'schools' => $schools,
			'file' => $file,
			'profilePicture' => $profilePicture,
			'skills' => $skills,
			'languages' => $dataSL["languages"],
			'curriculums' => $curriculums,
			'messages' => $messages
		);
		
		if(!empty($data['candidate'][$uid])){
			if($id_candidate > 0){
				if(!$array) {
					if($data["candidate"][$uid]["view"] == 0)
						return _twig("candidatos/timeline_candidate_view.twig", $vars);
					else
						return _twig("candidatos/classic_candidate_view.twig", $vars); 					
				} else {
					return $data;
				}
			}else{
				$this->view("candidato", $vars); 	
			}
		}
    }
	
	public function citar(){
		
		$candidate_id = $this->Get(1);
		$user = $this->verifyAccess();
		$subject = "Tienes una cita!";
		
		if(!empty($candidate_id)){									
													
			$post = POST::isReady();
			if($post){
				
				$data = POST::get('contact');								
				
				if(!empty($data)){
				
					$response = array();															
					$candidatesModel = $this->model(); 										
					$id_company = _Company::getCompanyID($user['uid']);
					$company = _Company::allData($id_company);
					$data['company'] = $company['name'];					
					$body = utf8_decode(_twig('mails/cita.twig', $data));					
					$mail = new Mail("default", utf8_decode($data['subject']), $body);					
					$mail->addAddress($data['email'], $data['full_name']);
					$date = substr($data['Date'], 7, 4).'-'._Catalogs::getMonthId(substr($data['Date'], 3, 3)).'-'.substr($data['Date'], 0, 2);        
					$data = $candidatesModel->invite( $candidate_id, $id_company, $date.' '.$data['Hour'].':'.$data['Minute'].':00');

					// Notifiacion en inbox //
					$inbox = _Messages::addMessage($user['uid'], $candidate_id, $subject, $body, date("Y-m-d H:i:s"));
					
					if($mail->send()){
						$response['msg'] = 'El mensaje ha sido enviado.';
						$response['status'] = 1;					
					}else{
						$response['msg'] = '2. Ha ocurrido un error, favor de intentarlo nuevamente.';
						$response['status'] = 0;
						$response['error'] = $mail->error();				
					}
				}else{
					$response['msg'] = '1. Ha ocurrido un error, favor de intentarlo nuevamente.';
					$response['status'] = 0;
				}
				
				echo json_encode($response);
				
			}else{
				$candidatesModel = $this->model();       
				$dataC = $candidatesModel->find( $candidate_id );
				$user = $this->verifyAccess();
				$id_company = _Company::getCompanyID($user['uid']);					
				$dataCO = $this->controller('admin')->dataCompany($id_company);			
				/*echo '<pre>';
				print_r($dataCO);
				echo '</pre>';*/
				$this->view("citar", array('candidate' => $dataC['candidate'][$candidate_id], 'user' => $user, 'company' => $dataCO['company'][0]));	
			}
		}
	}
	
	
	function sendEmailEstatus( $id_candidate, $status ){
	
		$data = $this->candidato( $id_candidate, true);		
		$data['candidate'][$id_candidate]['status'] = $status;
		$email = Config::get("Theme.Globals.".$status);
		$subject = $data['candidate'][$id_candidate]['name'].' '.$data['candidate'][$id_candidate]['last_name'].' ha sido '.($status == 'emailToHire' ? 'Contratado' : 'Dado de baja');
		$body = utf8_decode(_twig('mails/statusConfirm.twig', $data['candidate'][$id_candidate]));					
		$mail = new Mail("default", utf8_decode($subject), $body);					
		$mail->addAddress($email);      					
		$status = $mail->send();
		return array('email' => $email, 'status' => $status, 'uid' => $id_candidate);
		
	}
	
	
	function hire(){
		$candidates = POST::get('candidates');
		
		if(!empty($candidates)){
			$user = $this->verifyAccess();		
			$id_company = _Company::getCompanyID($user['uid']);
			$candidatesModel = $this->model();       
			$data = $candidatesModel->hire( $candidates, $id_company);
			
			if($data !== false){
				$response['msg'] = 'Los candidatos han sido contratados.';
				$response['status'] = 1;
				
				$candidates = !is_array($candidates) ? array($candidates) : $candidates;
				foreach($candidates as $candidate){
					$response[]['mails'] = $this->sendEmailEstatus( $candidate, 'emailToHire' ); // emailToHire||emailToDismiss					
				}
				
			}else{
				$response['msg'] = '1. Ha ocurrido un error, favor de intentarlo nuevamente.';
				$response['status'] = 0;				
			}
		}else{
			$response['msg'] = '2. Ha ocurrido un error, favor de intentarlo nuevamente.';
			$response['status'] = 0;				
		}
		
		echo json_encode($response);
	}
	
	function discard(){
		
		$candidates = POST::get('candidates');
		
		if(!empty($candidates)){
			
			$user = $this->verifyAccess();		
			$id_company = _Company::getCompanyID($user['uid']);
			$candidatesModel = $this->model();       
			$data = $candidatesModel->discard( $candidates, $id_company);
			
			if($data !== false){
				$response['msg'] = 'Los candidatos han sido descartados.';
				$response['status'] = 1;
			}else{
				$response['msg'] = '1. Ha ocurrido un error, favor de intentarlo nuevamente.';
				$response['status'] = 0;				
			}
		}else{
			$response['msg'] = '2. Ha ocurrido un error, favor de intentarlo nuevamente.';
			$response['status'] = 0;				
		}
		
		echo json_encode($response);
	}
	
	function dismiss(){
		
		$candidates = POST::get('candidates');
		$info = POST::get('data');

		foreach($info as $input){
			$dismiss[$input['name']] = $input['value']; 
		}
		
		if(!empty($candidates)){
			
			$user = $this->verifyAccess();		
			$id_company = _Company::getCompanyID($user['uid']);
			$candidatesModel = $this->model();       
			$data = $candidatesModel->dismiss( $candidates, $id_company, $dismiss);
			
			if($data !== false){
				$response['msg'] = 'Los candidatos han sido dados de baja.';
				$response['status'] = 1;
				$candidates = !is_array($candidates) ? array($candidates) : $candidates;
				foreach($candidates as $candidate){
					$response[]['mails'] = $this->sendEmailEstatus( $candidate, 'emailToDismiss' ); // emailToHire||emailToDismiss					
				}
			}else{
				$response['msg'] = '1. Ha ocurrido un error, favor de intentarlo nuevamente.';
				$response['status'] = 0;				
			}
		}else{
			$response['msg'] = '2. Ha ocurrido un error, favor de intentarlo nuevamente.';
			$response['status'] = 0;				
		}
		
		echo json_encode($response);
		
	}
	
	function test(){
		$candidates = POST::get('candidates');
		
		if(!empty($candidates)){
			$user = $this->verifyAccess();		
			$id_company = _Company::getCompanyID($user['uid']);
			$candidatesModel = $this->model();       
			$data = $candidatesModel->test( $candidates, $id_company);
			
			if($data !== false){
				$response['msg'] = 'Los candidatos seleccionados han sido registrados.';
				$response['status'] = 1;
			}else{
				$response['msg'] = '1. Ha ocurrido un error, favor de intentarlo nuevamente.';
				$response['status'] = 0;				
			}
		}else{
			$response['msg'] = '2. Ha ocurrido un error, favor de intentarlo nuevamente.';
			$response['status'] = 0;				
		}
		
		echo json_encode($response);	
	}
	
	
	function noHire(){
		
		$candidates = POST::get('candidates');
		
		if(!empty($candidates)){
			$user = $this->verifyAccess();		
			$id_company = _Company::getCompanyID($user['uid']);
			$candidatesModel = $this->model();       
			$data = $candidatesModel->noHire( $candidates, $id_company);
			
			if($data !== false){
				$response['msg'] = 'Operación exitosa.';
				$response['status'] = 1;
			}else{
				$response['msg'] = '1. Ha ocurrido un error, favor de intentarlo nuevamen5079te.';
				$response['status'] = 0;				
			}
		}else{
			$response['msg'] = '2. Ha ocurrido un error, favor de intentarlo nuevamente.';
			$response['status'] = 0;				
		}
		
		echo json_encode($response);
		
	}


	function notification(){
            if(POST::isReady() && POST::exists("salt")){
                if(POST::get("salt") == "78543316547812168744"){
                    $candidatesModel = $this->model();       
                    $data = $candidatesModel->candidatesInTest();

                    if(!empty($data['candidates'])){
                            $companies = $this->controller('admin')->companyUsers($data['companies']);

                            foreach ($data['candidates'] as $key => $value) {
                                    $value['last_date'] = substr($value['last_date'], 8, 2).' de '._Catalogs::getMonth(substr($value['last_date'], 5, 2)).' de '.substr($value['last_date'], 0, 4);
                                    $subject = utf8_decode($value['name'].' '.$value['last_name'].' concluiría sus practicas profesionales.');
                                    foreach ($companies as $key2 => $value2) {
                                            if($value['id_company'] == $value2['id_company']){
                                                    $data['candidates'][$key]['company_users'][] = $value2;																						
                                                    echo $body = utf8_decode(_twig('mails/notification.twig', array('candidate' => $value, 'user' => $value2)));
                                                    $mail = new Mail("default", $subject, $body);	
                                                    $mail->addAddress($value2['email'],utf8_decode($value2['name'].' '.$value2['last_name']));
                                                    if($mail->send()){
                                                        $message = " [SUCCESS] TO:".$value2['email']." Candidate:".$value['name'].' '.$value['last_name'];
                                                    }else{
                                                        $message = " [ERROR]   TO:".$value2['email']." Candidate:".$value['name'].' '.$value['last_name']." ERROR-DATA: ".$mail->error();
                                                    }
                                                    log::toFile("Mail alerts",$message,"/home/web/projects/logs/pilares/emailalerts.log");
                                            }
                                    }		
                            }	
                    }else{
                            echo utf8_decode('No hay candidatos que concluiran sus practicas profesionales en 3 o 5 días.');
                    }
                }
            }		
	}


	function addNotes(){
		$post = POST::isReady();
		$user = $this->verifyAccess();
		if($post){		
			$candidatesModel = $this->model(); 
			$result = $candidatesModel->addNote(array(
				'note' => POST::get('txt'),
				'id_candidate' => POST::get('id_candidate'),
				'uid' => $user['uid']
			));
			$response['status'] = 0;
			if($result !== false){
				$response['status'] = 1;
			}

			echo json_encode($response);
		}
	}


	function getNotes(){
		$post = POST::isReady();
		$user = $this->verifyAccess();
		if($post){		
			$candidatesModel = $this->model(); 
			$data = $candidatesModel->getNotes(array(
				'id_candidate' => POST::get('id_candidate'),
				'id_company' =>  _Company::getCompanyID($user['uid'])
			));
						
			$this->view("notes", $data );
		}	
	}


	function getDocuments(){
		$post = POST::isReady();
		$user = $this->verifyAccess();
		if($post){		
			$candidatesModel = $this->model(); 
			$data = $candidatesModel->getDocumentsCompany(array(
				'id_candidate' => POST::get('id_candidate'),
				'id_company' =>  _Company::getCompanyID($user['uid'])
			));
						
			$this->view("documents", array('documents' => $data) );
		}	
	}


	function getInterviewsDates(){
		$post = POST::isReady();
		$user = $this->verifyAccess();
		if($post){		
			$candidatesModel = $this->model(); 
			$data = $candidatesModel->getInterviewsDates(array(
				'id_candidate' => POST::get('id_candidate'),
				'id_company' =>  _Company::getCompanyID($user['uid'])
			));
						
			echo json_encode($data);
		}	
	}

	public function profileImageUpload() {
        $post = POST::isReady();
        $user = $this->verifyAccess();
        if($post) {
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
                $_FILES,
                $user['uid'],
                Config::get('Theme.Web.uploads'),
                'scan/pdfs/', 
                array(
                    'pdf',
                    'doc',
                    'docx',
                    'jpeg',
                    'jpg',
                    'bmp',
                    'gif',
                    'png'
                ),
                '2M',
                $uploadFiles::ENCRYPT_WITHOUT_EXT
            );

            if( !empty($response) ) {
                $backProfileData = $response[0]["file_name"]."|".POST::get("backposition");
                $dataResponse = array(
                	"file_name" => $response[0]["file_name"],
                	"position" => POST::get("backposition")
                );
                $candidatesModel = $this->model();
                $result = $candidatesModel->updateBackgroundProfile($user["uid"], $backProfileData, "background");
                print(json_encode($dataResponse));
            }
        }
    }

    public function showFile() {
    	$file_name = $this->Module("uploadFiles")->viewFile(Config::get('Theme.Web.uploads'), $this->Get(1), false);
    }

    public function showAvatar() {
    	if($this->Get(2) == "profile") {
    		$file_name = $this->Module("uploadFiles")->viewFile(Config::get('Theme.Web.uploads')."profile_pictures", $this->Get(1), false);
    	} else {
    		$file_name = $this->Module("uploadFiles")->viewFile(Config::get('Theme.Web.uploads'), $this->Get(1), false);
    	}
    }

    public function sendMessage() {
    	$post = POST::isReady();
        $user = $this->verifyAccess();
        if($post) {
        	$message = POST::get("message");

        	$response = _Messages::addMessage($user["uid"], 1, $message["subject"], $message["body"], date("Y-m-d H:i:s"), "candidate", "company");
        	print_r($response);
        }
    }

    public function avatarUpload() {
    	$post = POST::isReady();
        $user = $this->verifyAccess();
        if($post) {
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
            
            $upload_dir = "profile_pictures"; 

            if (!is_dir( Config::get('Theme.Web.uploads').$upload_dir)) {
				mkdir( Config::get('Theme.Web.uploads').$upload_dir, '775', true );
			}

            $response = $uploadFiles->upload(
                $_FILES,
                $user['uid'],
                Config::get('Theme.Web.uploads').$upload_dir,
                'scan/pdfs/', 
                array(
                    'pdf',
                    'doc',
                    'docx',
                    'jpeg',
                    'jpg',
                    'bmp',
                    'gif',
                    'png'
                ),
                '2M',
                $uploadFiles::ENCRYPT_WITHOUT_EXT
            );

            if(!empty($response)) {
            	$candidatesModel = $this->model();
                $result = $candidatesModel->updateBackgroundProfile($user["uid"], $response[0]["file_name"], "avatar");
                print($response[0]["file_name"]);
            }
        }
    }
	
}

?>