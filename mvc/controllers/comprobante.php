<?php

class comprobante extends _controller{
    
    // Public views
    public function index(){     
        $user       = $this->verifyAccess();
        $candidate  = $this->Controller("candidatos")->getCandidate($user["uid"]);
        $module     = $this->Controller("scheduler")->getSchedule($user["uid"],"CDD");
        if(!is_null($module)){
            $idOffice = $module["id_office"];
            $date     = $module["date"];
            $time     = substr($module["time"], 0, 5);
            $officeDat= _Offices::getModule($idOffice);
        }else{
            $url = $this->Core("Domains.core")."/candidato/calendario/citas";
            header("location: ".$url);
        }
        $data["schedule"]["time"]       = $time;
        $data["schedule"]["year"]       = date("Y", strtotime($date));
        $data["schedule"]["month"]      = _Catalogs::getMonth(date("m", strtotime($date)));
        $data["schedule"]["day"]        = date("d", strtotime($date));
        $data["schedule"]["dayofweek"]  = _Catalogs::getDayOfWeek(date("w", strtotime($date)));
        $data["candidate"]["folio"]     = $candidate["folio"];
        $data["candidate"]["name"]      = $candidate["name"]." ".$candidate["last_name"];
        $data["candidate"]["age"]       = floor((time() - strtotime($candidate['birthdate']))/(60*60*24*365));
        $data["candidate"]["delegation"]= $candidate["delegation"];
        $data["module"]["name"]         = 0;
        $data["module"]["addres"]       = 0;
        $data["office"]                 = $officeDat;
        $data["map"]                    = Geo::googleStaticMap($officeDat["lat"], $officeDat["lon"], 440, 260, 17, 'roadmp', '.', 'blue');        
        header('Content-Type: text/html; charset=utf-8');
        $print = $this->get(1);
        if(!empty($print)){
                $data['action'] = 'print';			
                twig('mails/comprobante.twig', $data);
        }else{
                $this->view("index",$data);
                $body = utf8_decode(_twig('mails/comprobante.twig', $data));
                $mail = new Mail("default", 'Comprobante de cita', $body);
                $mail->addAddress($candidate['email'], $candidate['name'].' '.$candidate['last_name']);
                $mail->send();
        }
		 
    }
        
}

?>