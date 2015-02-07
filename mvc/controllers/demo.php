<?php
class demo extends _controller{
    
    public function index(){
        $capchaModule = $this->Module("capcha");
        $data["module"]["capcha"]["capcha"] = $capchaModule->capchaView("Type what you see!");
        $this->view("index", $data);
    }
    
    public function capchaCheck(){
        $post = POST :: isReady();
        if(!empty($post)){
            //  for a easier match, let's convert lower case both variables
            //  the generated & user entered capcha
            $postCapcha = strtolower(POST :: get('input-capcha-capcha'));
            $capcha = strtolower(SESSION::get("capcha"));
            
            if($postCapcha == $capcha){
                echo "capcha validated";
            }else{
                echo "capcha NOT valid";
            }
        }
    }
}
?>