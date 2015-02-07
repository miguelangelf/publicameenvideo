<?php

class reportes extends _controller{
    
    // Public views
    public function contratados(){        
        $this->view("contratados"); 
    }
	
	public function evaluados(){        
        $this->view("evaluados"); 
    }
	
	public function registrados(){        
        $this->view("registrados"); 
    }
	
	public function aplicantes(){        
        $this->view("aplicantes"); 
    }
}

?>