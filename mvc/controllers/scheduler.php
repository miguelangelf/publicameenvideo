<?php

class scheduler extends _controller{
    
    public function availables($officeID,$slots){
        $model = $this->Model();
        $data  = $model->getAvailables($officeID,$slots);
        return $this->response(TRUE,$data);
    }
    
    public function schedule(){        
        if(POST::isReady()){
            if(POST::exists("valuedate")){
                $valueDate        = $this->Post("valuedate");
                $type             = 'CDD'; 
                list($date,$hour,$officeID,$candidateID) = explode('#', $valueDate);
                $model = $this->Model();
                $data = $model->schedule($date,$hour,$officeID,$candidateID,$type);
                if(!is_null($data)){
                    if($data["response"]=="OK"){
                        echo $this->response(TRUE, $data);
                    }else{
                        echo $this->response(FALSE, $data);
                    }
                }else{
                    echo $this->response(FALSE, "Error");
                }
            }
        }
    }
    
    public function getSchedule($uid,$type){
        $model = $this->Model();
        $data  = $model->getSchedule($uid,$type);
        if(!is_null($data)){
            return $data;
        }else{
            return null;
        }
    }
    
    private function response($end,$data){
        if($end){
            return json_encode(array("response"=>"OK","data"=>$data));
        }else{
            return json_encode(array("response"=>"KO","error"=>$data));
        }
    }
	
	
    public function accessToSchedule( $uid ){
        $scheduleModel = $this->model();
        return $scheduleModel->accessToSchedule( $uid );
    }
	
}