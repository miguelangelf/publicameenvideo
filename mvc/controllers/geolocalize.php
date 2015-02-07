<?php

class geolocalize extends _controller{
    
    // Public views
    public function index(){        

        //$geoModule = $this->Module("mod_geo");
        //$data["module"]["mod_geo"] = $geoModule->getGeo();
        $data = "Hellooo";
        //$getcity = $this->Get(1);
        $this->view("index", $data); 
                    
    }
    
}