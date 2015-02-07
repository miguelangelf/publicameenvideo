<?php

class cuestionario extends _controller{
    
    // Public views
    public function index(){
		$token = $this->get(1);
		if(!empty($token)){        
			$uid = $this->controller('registro')->getUid( $token );
			if(!empty($uid)){
				$etnias = _Catalogs::etnias();					
				$this->view("index", array('token' => $token, 'etnias' => $etnias)); 
			}else{
				echo "Acceso denegado";
			}       
		}else{
			echo "Acceso denegado";
		}
    }
	
	
	
    public function empresa(){ 
		$token = $this->get(1);
		if(!empty($token)){        
			$uid = $this->controller('registro')->getUid( $token );
			if(!empty($uid)){				
				$sectors =  _Catalogs::sectors();		
				$this->view("empresa", array('token' => $token, 'sectors' => $sectors)); 
			}else{
				echo "Acceso denegado";
			}       
		}else{
			echo "Acceso denegado";
		}       
    }
	
    public function registro_exitoso(){        
        $this->view("registro_exitoso"); 
    }
}

?>