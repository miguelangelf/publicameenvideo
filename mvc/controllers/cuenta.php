<?php

class cuenta extends _controller{
    
    // Public views
    public function index($view = null){
        $user = $this->verifyAccess();
		//print_r($user);

        // Agregamos un Mensaje //
        //$message_id = _Messages::addMessage(155, 1, "Oferta de Trabajo", "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s", date("Y-m-d H:m:s"));
        //$messages = _Messages::getMessages(1, null);
        //$response = _Messages::deleteMessage(5);
        if(!empty($user['uid'])){

            $jobs = $this->controller("configuracion")->getWork("php");
            $schools = $this->controller("configuracion")->getSchools("php");
            $dataSL = $this->controller("configuracion")->getSLs();
            $curriculums = $this->controller("configuracion")->curriculum("php");
            $html = $this->controller("candidatos")->candidato($user['uid'], null, $jobs, $schools, $dataSL, $this->Get(1), $curriculums);
            $schedule = $this->controller("scheduler")->accessToSchedule($user["uid"]);

            if(!empty($html)){
		      $this->view('index', array('profile' => $html, 'schedule' => $schedule, 'roles' => $user['roles'][0], 'jobs' => $jobs));        
            }
        }
    }

    public function messages_company() {
        $user = $this->verifyAccess();

        $post = POST::isReady();
        $id = POST::get("id");
        if($post && isset($id)){
            $result = _Messages::updateFlag(POST::get("id"));
            print($result);
        } else {
            $messages = _Messages::getMessages($user["uid"], $user["name"], "outbox");
            $this->view("messages_company", array('messages' => $messages));
        }
    }

}

?>