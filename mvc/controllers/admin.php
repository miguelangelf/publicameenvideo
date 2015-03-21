<?php

class admin extends _controller {

    public function dashboard() {        
        $this->view("dashboard", null);
    }
    
    public function countuser()
    {
        
        $data['countusers']=5;
        $this->view("dashboard",$data);
    }
    
    public function usuarios(){
        $inbox_number = $this->Post("inbox");
        $nextpage = $this->Post("next");
        $categoria = $this->Post("cat");
        $search = $this->Post("message");
        $data["inbox"] = $inbox_number." :: ".$categoria;
        
        $users = $this->Model()->users($nextpage,$search);
        $data["users"] = $users;
        
        $usersnumber = $this->Model()->countusers($nextpage,$search);
        $data["max"] = $users;
        
        
        $this->view("usuarios", $data);
    }
    
    
    public function empresas(){
        $inbox_number = $this->Post("inbox");
        $data["inbox"] = $inbox_number;
        
        $users = $this->Model()->empresas();
        $data["users"] = $users;
        
        
        $this->view("empresas", $data);
    }
    
    
    public function videos(){
        $inbox_number = $this->Post("inbox");
        $data["inbox"] = $inbox_number;
        
        $users = $this->Model()->videos();
        $data["users"] = $users;
        
        
        $this->view("videos", $data);
    }
    
    public function notificaciones(){
        $type = $this->Get(1);
        switch($type){
            case "usuarios":
                $disable_users = $this->Model()->disable_users();
                break;
        }
        echo json_encode(array("no_users"=>$disable_users));
    }
    
}

?>