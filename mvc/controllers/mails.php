<?php

class mails extends _controller{
    
    // Public views
    public function index(){        
        $this->view("cita"); 
    }
    
    public function confirmacion(){        
        $this->view("confirmacion"); 
    }
    
    public function comprobante(){        
        $this->view("comprobante"); 
    }
    
    public function comprobante2(){        
        $this->view("comprobante2"); 
    }
    
    public function no_show(){        
        $this->view("no_show"); 
    }
    
    public function info_candidato(){
		
		$isPost = POST::isReady();				
		if($isPost){
			$uid = POST::get('id_candidate');
			$email = POST::get('email');
			$response['status'] = 0;
			if(!empty($uid)){
				$data = $this->controller('candidatos')->candidato($uid, true);
				$body = utf8_decode(_twig('mails/info_candidato.twig', array('candidate' => $data['candidate'][$uid])));
				$subjet = 'Perfil de '.$data['candidate'][$uid]['name'].' '.$data['candidate'][$uid]['last_name'];
				$mail = new Mail("default", utf8_decode($subjet), utf8_decode($body));					
				$mail->addAddress($email);      				
				if($mail->send()){
					$response['status'] = 1;
					$response['msg'] = '<i class="fa fa-check"></i> &nbsp;El perfil ha sido enviado correctamente';
				}else{		
					$response['msg'] = '2.Ha ocurrido un error';			
				}
			}else{
				$response['msg'] = '1.Ha ocurrido un error';	
			}
			echo json_encode($response);
		}else{        
			$uid = $this->Get(2);
			if(!empty($uid)){
				$data = $this->controller('candidatos')->candidato($uid, true);
				$action = $this->Get(1);	
				header('Content-Type: text/html; charset=utf-8');						
				$this->view("info_candidato", array('candidate' => $data['candidate'][$uid], 'action' => $action));
			}else{
				echo "Acceso denegado";
			}
		}
    }
}

?>