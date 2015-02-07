<?php

class jobmarkers extends _controller{
    
    const TIME = 3;    // Hours
    
    public function json(){
        date_default_timezone_set("America/Mexico_City");
        
        $file_path = Config::get("Core.Path.theme").'/data/map_points.json';
        $generate = false;
        
        if (file_exists($file_path)){
            $file_date = filemtime($file_path);
            $current_date = time();        
            $diff = $current_date - $file_date;
            $interval = self::TIME * 60 * 60;
            
            if ($diff > $interval){
                $generate = true;
            }
        }else{
            $generate = true;
        }

        if ($generate){
            $model = $this->Model();
            $jobs = array(
                    "points"=>$model->getMarkers()
                );
            $json = json_encode($jobs);
            $fp = fopen($file_path,"w");
            fwrite($fp,$json);
            fclose($fp);
        }
        $content = file_get_contents($file_path);
        header('Content-Type: application/json');
        echo $content;
    }
}
