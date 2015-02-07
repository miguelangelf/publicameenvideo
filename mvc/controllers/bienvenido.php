<?php

class bienvenido extends _controller {
	
	const CANDIDATE_USER = 4;

    // Public views
    public function index() {
        //$data["title"]  = l("Home");
        //print_r($_SESSION); 
        $menu  = $this->Module("sidemenu")->init("menu1","filters","right","slide");
        
        //$register = SESSION::get('_SDFGA_5739_AUKK_REFER_');
        //if (!empty($register)) {
        //    $this->view("session_active");
        //} else {
            $data["home"] = 'open';    //Show active menu in header.twig
            $data["demo1"] = $this->Module("demo1")->demo();
            $data["Sidemenu1"] = $menu->html();
            //show the module demo1 with the function dbData()
		$homeModel = $this->model();
		$data['vacancies'] = $homeModel->topVacancies();
		//_Catalogs::pr($data);
            $this->view("home", $data);
       // }
    }


    public function typography() {
        $data["typography"] = 'open';  //Show active menu in header.twig
        $this->view("examples", $data);
    }

    public function sitemap() {
        $data["sitemap"] = 'open';  //Show active menu in header.twig
        $this->view("sitemap", $data);
    }
    
    public function preguntas_frecuentes(){
		$data["demo1"] = $this->Module("demo1")->demo();
        $this->view("preguntas_frecuentes", $data);
    }
	
	public function contacto() {
		$data["demo1"] = $this->Module("demo1")->demo();
        $this->view("contacto", $data);
    }
    
    public function objetivos(){
		$data["demo1"] = $this->Module("demo1")->demo();
        $this->view("objetivos", $data);
    } 

    public function ofertas(){
        $menu  = $this->Module("sidemenu")->init("menu1","filters","right","slide");        
		$data = array();	
        $data["demo1"] = $this->Module("demo1")->demo();
        $data["Sidemenu1"] = $menu->html();
		$post = POST::isReady();
		if($post){
			$city = POST::get('jobs-location');
			$data['searchBy'] = POST::get('search-by').( !empty($city) ? ' city:"'.$city.'"' : "" );					
		}
        $this->view("ofertas", $data);
    }
	
    public function listado_ofertas(){			
            $params['rows'] = 10;
            $params['page'] = 1;
            $logged = SESSION::get('_SDFGA_5739_AUKK_REFER_');
			$homeModel = $this->model();
            if(POST::get('page')){
                    $param = POST::get('page');
                    $params['page'] = (!empty($param) && is_numeric($param)) ? $param : 1;
            }
			if(POST::get('searchBy')){
				$params['search'] = POST::get('searchBy');				
			}
            if(!empty($logged)){
                    $user = $this->verifyAccess();
                    if( $user['roles'][0] == self::CANDIDATE_USER){
                            $params['uid'] = $user['uid'];
                    }
            }	                        
            $data = $homeModel->find( $params );
			if(!empty($data['total_rows']) && !empty($params['search'])){
				$uid = !empty($params['uid']) ? $params['uid'] : 0;
				$homeModel->logSearching( $params['search'], $data['total_rows'], $uid );	
			}
            $json = $this->groupofertas($params);
            //_Catalogs::pr($data);	
            twig('vacancies/published_list.twig', array('jsongroups'=>$json,'vacancies' => $data, 'total_rows' => $data['total_rows'], 'open' => 1));	        
            //$json = array("jobs"=>$v);
            //echo json_encode($json,TRUE);
    }
    
    public function groupofertas($params){
        return $this->model()->grouping($params);
    }
	
    public function detalle_oferta(){		
    	// print_r($_SESSION);
    	$prefix = Config::get("Core.Domains.core");

        $data["demo1"] = $this->Module("demo1")->demo();
        $menu  = $this->Module("sidemenu")->init("menu1","filters","right","slide");
        $data["Sidemenu1"] = $menu->html();
		$id_vacancy = $this->Get(1);	
		if(!empty($id_vacancy) && is_numeric($id_vacancy)){
			$this->applyToByToken( $this->Get(2), $id_vacancy );
			$logged = SESSION::get('_SDFGA_5739_AUKK_REFER_');			
			$homeModel = $this->model();														
			$capcha = false;
			$uid = false;
			$countries = array();
			if(empty($logged)){								
				$capchaModule = $this->Module("capcha");
				$capcha = $capchaModule->capchaView("Ingresa el código aquí");				
				$countries = _Catalogs::findCountries();
			}else{
				$user = $this->verifyAccess();
				if( $user['roles'][0] == self::CANDIDATE_USER){
					$uid = $user['uid'];
				}
			}						
			$data = $homeModel->findById($id_vacancy, $uid );
			$data['for_candidates'] = 1;			
			$data['open'] = 1;										
			$detail = _twig('vacancies/datail.twig', $data);				
		}
        $this->view(
			"detalle_oferta", 
			array(
				'detail'=>$detail, 
				'id_vacancy' => $id_vacancy, 
				'countries' => $countries, 
				'capcha' => $capcha, 
				'external' => $data['external']
			)
		);
    }
	
	
	function applyToByToken( $url, $id_vacancy ){		
		parse_str($url, $url_data );			
		if(!empty($url_data['aplicant'])){			
			$homeModel = $this->model();
			$data = $homeModel->applyData( $url_data['aplicant'], $id_vacancy );			
			if(!empty($data)){
				$candidate = $homeModel->getCandidateByUid($data['id_candidate']);
				if(!empty($candidate['token'])){
					$this->controller('registro')->activateCandidate( $data['id_candidate'] );					
				}
				Users::autoLogin($data['id_candidate'], $candidate['password']);				
				$response = $this->controller('vacancies')->applyTo( $id_vacancy, $data['external'], true, $data['id_candidate'] );
				if($response['success'] == 1){	
					$homeModel->applyDataUpdate( $url_data['aplicant'], $id_vacancy );														
				}else{
					//_Catalogs::pr($response);		
				}
			}
		}
	}
	
	
	function tokenToVacancyApply($uid, $id_vacancy){
		$vacanciesModel = $this->model();
		return $vacanciesModel->tokenToVacancyApply($uid, $id_vacancy);
	}

}

?>