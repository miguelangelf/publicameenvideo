<?php

class acceso extends _controller{

    const ROL_CAN = 4;
    const ROL_EMP = 5;
    const ROL_GOB = 6;
	const ROL_ADM = 7;
	const ROL_EMP_ADMIN = 8;   
    
    // Public views
    public function para(){        

        $uid = SESSION::get('_SDFGA_5739_AUKK_REFER_');        
        if(!empty($uid)){
           $prefix = Config::get("Core.Domains.core");
           $afterlogin = Config::get("Theme.Web.afterlogin");
           header("Location: ".$prefix.$afterlogin);            
        }

        $capchaModule = $this->Module("capcha");
        $data["module"]["capcha"]["capcha"] = $capchaModule->capchaView("Ingresa el código aquí");
        
        $type = $this->Get(1);
        switch ($type) {
            case 1:
                 $this->view("gobierno");
                break;
            case 'empresas':
                 $this->view("empresa", $data);
                break;
            case 'candidatos':
                 $this->view("candidato", $data);
                break;
	    	case 4:
                 $this->view("admin");
                break;    
            default:
                header("Location:".Config::get("Core.Domains.core"));
                break;            
        }
                    
    }
    
    public function login(){
        
        $post = POST :: isReady();
        if(!empty($post)){
            
            $postData = POST :: get('_token');
            $candidate = POST :: get('_candidate');            
            $dataAccess = $this->decryptDataAccess($postData);        
			$email = $dataAccess->UserEmail;
            
			if ($candidate == 1) {
				$accesModel = $this->model();
                $user = $accesModel->getUserByEmail($email);
				if(empty($user['id_status']) && !empty($user['token'])){
                	$_candidate = $this->controller('candidatos')->getCandidate( $user['uid'] ); 
					if( !empty($_candidate['id_city']) ){
						$this->controller('registro')->activateCandidate( $user['uid'] );
					}
				}
            }						                            
            
            $acces = Users::login($email, $dataAccess->UserPassword);          

            if(!empty($acces)){
                $data['success'] = 1;
                $prefix = Config::get("Core.Domains.core");
                $data['afterLogin'] = $prefix.Config::get("Theme.Web.afterlogin");
            }else{
                $data['success'] = 0;
            }
            
            echo json_encode($data);
            
        }
        
    }


    public function redirect(){
        
        $user = $this->verifyAccess();
        $prefix = Config::get("Core.Domains.core");
		
		if(!empty($user['uid'])){
			switch ($user['roles'][0]) {
				case self::ROL_CAN:
					$start_page = 'candidato/cuenta';
					break;				
				case self::ROL_GOB:
					$start_page = 'gobierno/agendar';
					break;
				case self::ROL_EMP_ADMIN:	
				case self::ROL_EMP:
					$start_page = 'empresa/candidatos/todos';
					//$start_page = 'empresa/busquedas';
					break;
				case self::ROL_ADM:
					$start_page = 'web/admin/inicio';
					break;    
			}        
				     
			//header("Location: ".$prefix.$start_page);		
                                                                        
                }else{
                    $start_page = Config::get("Theme.Web.landing");
                }
                
        $this->view('redirect', array('Location' => $prefix.$start_page));

    }

    
    private function decryptDataAccess($dataAccessEncrypt) {
        $logfile = Config::get("Core.OpenSSL.log");
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
            2 => array("file", $logfile, "a") // stderr es un fichero para escritura
        );

        //$key = SESSION::get("_JJSSP_5830_AUSS_REFER_");

        // Decrypt the client's request and send it to the clients(uncrypted)
        //$cmd = sprintf("openssl enc -aes-256-cbc -pass pass:" . escapeshellarg($key) . " -d");
        $cmd = sprintf('openssl enc -aes-256-cbc -pass pass:"" -d');
        $process = proc_open($cmd, $descriptorspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], base64_decode($dataAccessEncrypt));
            fclose($pipes[0]);

            $data = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            proc_close($process);
        }        
        
        return json_decode($data);                                        
        
    }
	
	
	function resetPassword(){
		$post = POST :: isReady();
        if(!empty($post)){
			$email = POST::get('email');			
			$response['status'] = 0;
			$response['msg'] = 'No existe un cuanta asociada a este email.';
			if(!empty($email)){
				$accesModel = $this->model();
                $user = $accesModel->getUserByEmail($email);
				if(!empty($user)){
					$token = md5(uniqid(mt_rand(), true));
					$result = $accesModel->setToken($user['uid'], $token);
					if($result !== false){
						$user['token'] = $token;
						$body = utf8_decode(_twig('mails/resetPassword.twig', $user));
						$mail = new Mail("default", utf8_decode('Cambiar contraseña'), $body);
						$mail->addAddress($user['email'], $user['name'].' '.$user['last_name']);
						$send = $mail->send();
						if($send){
							$response['status'] = 1;
							$response['msg'] = 'Se te ha enviado un email para cambiar tu contrase&ntilde;a.';
						}else{
							$response['msg'] = '2. Ha existido un error';		
						}						
					}else{
						$response['msg'] = '1. Ha existido un error';	
					}
				}
			}
			echo json_encode($response);
		}
	}


    function testCandatate(){
        Users::autoLogin(1, 'ed8a9d6609dffc0d00b97510f73eb64dd09a8e700ddf9485');
        header('Location: '.Config::get("Core.Domains.core").'candidato/cuenta');
    }
    
}

?>