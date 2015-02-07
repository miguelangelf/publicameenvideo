<?php

class registro extends _controller{    

    // Public views
    public function index(){    

        $post = POST :: isReady();
        if(!empty($post)){

            $candidate = POST :: get('User'); 
			$response['status'] = 0;                     
            //  for a easier match, let's convert lower case both variables
            //  the generated & user entered capcha
            $postCapcha = strtolower(POST :: get('input-capcha-capcha'));
            $capcha = strtolower(SESSION::get("capcha"));
            
            //  Let's validate the capcha or let's stop the process
            if($postCapcha != $capcha){
                $response['msg'] = '4. El c&oacute;digo es incorecto.';
            }else{
                if(!empty($candidate)){
					 $modelRegistro = $this->model();					
					$candidate['carrer_site'] = 0;
					if(!isset($candidate['password'])){
						$candidate['password'] = $this->randomPassword();						
						$candidate['carrer_site'] = 1;
					}															                   
                    $uid = $modelRegistro->addCandidate($candidate);

                    if(is_numeric($uid)){            
                        if($uid > 0){
							if(!empty($_FILES)){
								$r_uploadFiles = $this->saveCV( $_FILES, $uid );
								$email = $this->get(1);
								if( $email == 'vacancy'){
									$id_vacancy = $this->get(2);
									if(!empty($id_vacancy) && is_numeric($id_vacancy)){
										$vacancy = $this->controller('bienvenido')->tokenToVacancyApply($uid, $id_vacancy);		
									}
								}						
							}							
                            $candidate['uid'] = $uid;
							$candidate['vacancy'] = $vacancy['data'];
                            $this->sendEmailConfirmation($candidate);
                            $response['msg'] = 'Tu cuenta ha sido creada, se te ha enviado un email para activarla.';						
                            $response['status'] = 1;                                    
                        }else{						
                            switch($uid){
                                case $modelRegistro::ERROR_EMAIL:
                                    $response['msg'] = 'Ya existe un usuario registrado con este email';
                                break;
                                case $modelRegistro::ERROR_CURP:
                                    $response['msg'] = 'Ya existe un usuario registrado con este CURP';
                                break;
                                default:
                                    $response['msg'] = '1. Existio un error';
                                break;	
                            }                       
                        }
                    }else{
                        $response['msg'] = '3. Existio un error.';						
                    }
                }else{
                    $response['msg'] = '2. Existio un error';                
                }
            }
            echo json_encode($response);
        }

    }
    
    private function generateFolio($gender,$id_delegation,$uid){
        $folio = "".$gender;
        $folio .= strlen("".$id_delegation) == 1 ? "0".$id_delegation : $id_delegation;
        $folio .= str_pad("".$uid, 6, "0", STR_PAD_LEFT);
        return $folio;
    }
	
	public function cuestionario(){
		$post = POST :: isReady();
        if(!empty($post)){
			$response['status'] = 0;
                        $response['msg'] = 'Ha ocurrido un error.';
			$candidate = POST :: get('candidate');
			$modelRegistro = $this->model();
			$data = $modelRegistro->getCandidateUid($candidate['token']);                        
			if(!empty($data['uid']) && empty($data['id_status']) && !empty($candidate["id_delegation"])){
				$candidate['uid'] = $data['uid'];                                                                                                    
                                $candidate['folio'] = $this->generateFolio($candidate["gender"],$candidate["id_delegation"],$candidate['uid']);
                                $result = $modelRegistro->dataCandidate($candidate);
                                if($result!== false){
                                        if(is_array($result)){
                                            $response['msg'] = 'Ya existe un usuario registrado con este CURP';
                                        }else{
                                            $field = $modelRegistro->getCURP( $data['uid'] );
                                            if(!empty($field['curp'])){
                                                $resp = $this->activateCandidate( $data['uid'] );
                                                if($resp !== false){                                                    
                                                    $response['status'] = 1;
                                                    $response['msg'] = 'Registro exitoso';
                                                    Users::autoLogin($data['uid'], $data['password']);                                                    
                                                }
                                            }else{
                                                //error_log
                                                //error_log("[ ".date("Y-m-d H:i:s")." ] - Candidate( ".$data['uid']." ) - ".json_encode($candidate)." \n",3,"/home/web/projects/logs/gkm-core/activate_fail.log");
                                            }
                                        }
                                }                                
			}else{                            
                            $response['msg'] = 'Todos los campos marcados con "*" son requeridos ';
                        }
                        
			echo json_encode($response);
		}
	}	
	
	public function getUid( $token = ''){
		if(!empty($token)){
			$modelRegistro = $this->model();
			$data = $modelRegistro->getCandidateUid($token);
			if(!empty($data['uid']) && empty($data['id_status']) ){
				return $data['uid'];
			}else{
				return false;
			}
		}
	}
	
	public function sendEmailConfirmation( $candidate ){
		
		if(is_numeric($candidate['uid'])){
			$modelRegistro = $this->model();
			$data = $modelRegistro->getCandidateToken($candidate['uid']);
			if(!empty($data['token'])){
				$candidate['token'] = $data['token'];
				$body = utf8_decode(_twig('mails/confirmacion.twig', $candidate));
				$mail = new Mail("default", 'Confirma tu cuenta', $body);
				$mail->addAddress($candidate['email'], $candidate['name'].' '.$candidate['last_name']);
				$mail->send();
			}
		}
		
	}
	
	public function activateCandidate( $uid ){
		if(is_numeric($uid)){
			$modelRegistro = $this->model();
			return $modelRegistro->activateCandidate($uid);	
		}else{
                    return false;
                }
	}
	
	public function getColonies(){
		
		$response['status'] = 0;
		$post = POST :: isReady();
        if(!empty($post)){
			$cp = POST::get('cp');
			if(!empty($cp) && strlen($cp) == 5){				
				$data = _Catalogs::colonies($cp);
				if(!empty($data)){
					$response['status'] = 1;
					$response['catalog'] = $data;
				}else{
					$response['msg'] = 'Código Postal Inválido';
				}	
			}else{
				$response['msg'] = 'Código Postal Inválido';	
			}
			echo json_encode($response);
		}
		
	}
	
	public function createUserCompany(){
		
		$post = POST :: isReady();
        if(!empty($post)){

            $user = POST :: get('User'); 
			$response['status'] = 0;                     
            //  for a easier match, let's convert lower case both variables
            //  the generated & user entered capcha
            $postCapcha = strtolower(POST :: get('input-capcha-capcha'));
            $capcha = strtolower(SESSION::get("capcha"));
            
            //  Let's validate the capcha or let's stop the process
            if($postCapcha != $capcha){
                $response['msg'] = '4. El c&oacute;digo es incorecto.';
            }else{
                if(!empty($user)){

                    $modelRegistro = $this->model();
                    $uid = $modelRegistro->addCompanyUser($user);

                    if(is_numeric($uid)){            
                        if($uid > 0){
                            $user['uid'] = $uid;
                            $this->sendEmailUser($user);
                            $response['msg'] = 'Tu cuenta ha sido creada, se te ha enviado un email para confirmarla.';						
                            $response['status'] = 1;                                    
                        }else{						
                           $response['msg'] = 'Ya existe un usuario registrado con este email';                                                
                        }
                    }else{
                        $response['msg'] = '3. Existio un error';
                    }
                }else{
                    $response['msg'] = '2. Existio un error';                
                }
            }
            echo json_encode($response);
        }		
		
	}
	
	public function sendEmailUser( $user ){
		
		if(is_numeric($user['uid'])){
			$modelRegistro = $this->model();
			$data = $modelRegistro->getCandidateToken($user['uid']);
			if(!empty($data['token'])){
				$user['token'] = $data['token'];
				$body = utf8_decode(_twig('mails/user_company.twig', $user));
				$mail = new Mail("default", 'Confirma tu cuenta', $body);
				$mail->addAddress($user['email'], $user['name'].' '.$user['last_name']);
				$mail->send();
			}
		}
		
	}
	
	public function getCompanyId( $token ){
		$modelRegistro = $this->model();
		$data = $modelRegistro->getCompanyId($token);
		if(!empty($data['id'])){
			return $data['id'];
		}else{
			return false;
		}	
	}	
	
	public function createCompany(){
		$post = POST :: isReady();
        if(!empty($post)){

            $user = POST :: get('user');
			$company = POST :: get('company'); 
			$response['status'] = 0;
			$modelRegistro = $this->model();
			$data = $modelRegistro->createCompany($company, $user);
			if($data !== false){
				$response['status'] = 1;                                
				$body = utf8_decode(_twig('mails/new_company.twig', $company));
				$mail = new Mail("default", utf8_decode('Nueva empresa - '.$company['name']), $body);
				$mail->addAddress( Config::get('Theme.Globals.newCompany') );
				$mail->send();
			}
			
			echo json_encode($response);
			
		}
		
	}
	
	
	public function dataCompanyUser( $user ){
		$modelRegistro = $this->model();
		return $modelRegistro->dataUser( $user );
	}
	
	
	public function getCandidateToken( $uid ){
		$modelRegistro = $this->model();
		return $modelRegistro->getCandidateToken( $uid );	
	}
	
	
	public function getCandidateByToken( $token ){
		$modelRegistro = $this->model();
		return $modelRegistro->getCandidateUid( $token );	
	}
	
	
	public function updateCandidate( $candidate ){
		$modelRegistro = $this->model();
		return $modelRegistro->dataCandidate($candidate);						
	}	
	
	
	public function getSubSector(){
		
		$post = POST :: isReady();
        if(!empty($post)){
			$id_sector = POST::get('id_sector');
			if(!empty($id_sector) && is_numeric($id_sector)){				
				$data = _Catalogs::subSector($id_sector);
				if(!empty($data)){
					$response['status'] = 1;
					$response['catalog'] = $data;
				}else{
					$response['msg'] = 'Sector Inválido';
				}	
			}else{
				$response['msg'] = 'Sector Inválido';	
			}
			echo json_encode($response);
		}
		
	}
	
	function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
	
	
	function saveCV( $file, $uid ){
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
									
		$r_uploadFiles = $uploadFiles->upload(
			$file, //FILES via POST
			$uid,	//uid 
			Config::get('Theme.Web.uploads'), //Directory from uploads 
			'curriculums/', //Constant of upload path  
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
		
		if(!empty($r_uploadFiles[0]['fid'])){
			$id = $this->controller('configuracion')->saveApplyCV( $r_uploadFiles[0] );
			if(!empty($id)){
				return $id;
			}else{
				return false;
			}
		}
	}
	
}

?>