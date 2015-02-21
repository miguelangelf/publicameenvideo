<?php

class admin extends _controller {

    public function dashboard() {        
        $data = array("username"=>"Sergio Roman","gender"=>0,"var"=>$this->Get(1));
        $this->view("dashboard", $data);
    }
    
    public function hello() {
        echo "HELLO";
        exit();
        //$this->view("dashboard", array());
    }
}

?>