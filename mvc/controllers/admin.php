<?php

class admin extends _controller {

    public function dashboard() {        
        $data = array("username"=>"Sergio Roman","gender"=>0,"var"=>$this->Get(1));
        $this->view("dashboard", $data);
    }
    
    public function usuarios(){
        $inbox_number = $this->Post("inbox");
        $data["inbox"] = $inbox_number;
        
        $users = $this->Model()->users();
        $data["users"] = $users;
        
        
        $this->view("usuarios", $data);
    }
    
}

?>