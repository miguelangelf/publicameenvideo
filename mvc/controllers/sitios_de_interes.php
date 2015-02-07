<?php
class sitios_de_interes extends _controller{
	
    public function index(){		
        $data["demo1"] = $this->Module("demo1")->demo();
        $this->view("index", $data); 
    }
	
}
?>