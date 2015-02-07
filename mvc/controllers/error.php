<?php

class error extends _controller{
    
    // Public views
    public function mvc(){
        $data                       = $this->controller("web")->loadGlobal();
        $err                        = $this->Get(1);
        $data["title"]              = "Oops !!";
        $data["error_no"]           = $err;
        switch($err){
            case 1:
                $data["error_name"]         = "Controller not found.";
                $data["error_description"]  = "The controller <strong><i>".$this->Get(2)."</i></strong> not exists.";
                break;
            case 2:
                $data["error_name"]         = "Action not found.";
                $data["error_description"]  = "The action <strong><i>".$this->Get(3)."</i></strong> not exists in the controller <strong><i>".$this->Get(2)."</i></strong>";
                break;
        }
        $this->view("generic_error",$data);
    }
    
    public function page404(){
        $data                       = $this->controller("web")->loadGlobal();
        $data["title"]              = "Oops !!";
        $data["error_no"]           = 404;
        $data["error_name"]         = "Page not found";
        $data["error_description"]  = "Sorry, The page does not exists in the web.";
        $this->view("generic_error",$data);
    }
    
}

class ERRNO{
    public static $err = array(
        1 => array("Controller not found",""),
        2 => array("Page not found",""),
        404 => array("Page not found",)
    );
    
    public static function get($no){
        return self::$err[$no];
    }
}

?>