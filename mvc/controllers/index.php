<?php

class index extends _controller{
    
    // Public views
    public function index(){
        header("location: ".Theme::basePath()."/web/bienvenido");
    }
}