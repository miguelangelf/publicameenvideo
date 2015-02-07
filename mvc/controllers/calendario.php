<?php

class calendario extends _controller{
        
    public function citas(){
    	$user = $this->verifyAccess();		
        $schedule = $this->controller('scheduler')->accessToSchedule($user["uid"]);		
        if(!$schedule){
                $this->controller('acceso')->redirect();
        }		
        $candidateID = $user["uid"];
        $candidate   = $this->Controller("candidatos")->getCandidate($candidateID);
        $officeData  = _Offices::getModuleByCP($candidate["zip_code"]);

	if(empty($officeData["id"])){
		$officeData = _Offices::getModule(1);	
	}
	
        $data["candidate"]["age"] = floor((time() - strtotime($candidate['birthdate']))/(60*60*24*365));
        $data["map"] = Geo::googleStaticMap($officeData["lat"], $officeData["lon"], 450, 310, 17, 'roadmp', '.', 'blue');
        
        // Entrevista
        $scheduler = $this->Controller("scheduler");
        
        $officeID  = $officeData["id"];
        $slots     = 700;
        $fetchdata = $scheduler->availables($officeID,$slots);        
        $fetchdata = (array)json_decode($fetchdata,TRUE);
        
        $dates = array();
        $index = 0;
        foreach($fetchdata["data"] as $element){
            // Fix CODE
            if($element["Date"] != "2014-10-08" && $element["Date"] != "2014-10-09" && $element["Date"] != "2014-10-10" && $element["Date"] != "2014-10-13"){
                $dates[$element["Date"]]["times"][]     = array("time"=>substr($element["time"], 0, 5),"id"=>$element["id"],"value_date"=>$element["Date"]."#".$element["time"]."#".$officeID."#".$candidateID); 
                $dates[$element["Date"]]["month"]       = _Catalogs::getMonth(date("m", strtotime($element["Date"])));
                $dates[$element["Date"]]["day"]         = date("d", strtotime($element["Date"]));
                $dates[$element["Date"]]["dayofweek"]   = _Catalogs::getDayOfWeek(date("w", strtotime($element["Date"])));
                if(count($dates) == 5){
                    unset($dates[$element["Date"]]);
                    break;
                }
                $index ++;
            }
        }
        $data["Scheduler"]["Slots"] = $dates;
        $data["Module"] = $officeData;
        
        $this->view("citas", $data); 
    }
	
    public function entrevistas(){        
        $this->view("entrevistas"); 
    }
			
}

?>