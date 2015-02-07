<?php

class vacancies extends _controller{
	
	const COMPANY_USER_ADMIN = 8;
	const CANDIDATE_USER = 4;

	public function index(){

		$user = $this->verifyAccess();
		$params['rows'] = 10;
		$params['id_company'] = _Company::getCompanyID($user['uid']);
		$params['logged_user'] = $user['uid'];
		if($this->Get(1) == 'page'){
			$param = $this->Get(2);
			$params['page'] = (!empty($param) && is_numeric($param)) ? $param : 1;
		}
		
		$search = POST::get('search');
		$params["search"] = $search;
		
		$vacanciesModel = $this->model();
		$rows = $vacanciesModel->find( $params );
		//_Catalogs::pr($rows);

		$pages =  Paginator::pagination(array(
			'total_rows' => $rows['total_rows'], 
			'url' => Config::get("Core.Domains.core").'empresa/vacancies/index/',
			'current_page' => $params['page'],
			'page_rows' => $params['rows'],
			'max_pages' => 5,
			'next' => '>',
			'first' => '<<',			
			'last' => '>>',
			'previous' => '<',
			'info' => '%total_rows% Vacantes(s) en total'
			//'Page %page% of %pages%, showing %page_rows% records out of %total_rows% total, starting on record %start%, ending on %end%'			
			)
		);

		$this->view('index', array('vacancies' => $rows, 'pagination' => $pages, 'total_rows' => $rows['total_rows'], 'logged_user' => $user['uid'], 'role' => $user['roles'][0]));
	}


	function manager(){
		
		$user = $this->verifyAccess();		
		$vacancy_id = POST::get('vacancy_id');
		$vacancy = array();
		$cities = array();
		$id_country = 'MX';
		$id_state =
		$vacanciesModel = $this->model();
		$users = array();

		if(is_numeric($vacancy_id)){			
			$vacancy = $vacanciesModel->findById($vacancy_id);	
		}

		if(!empty($vacancy['id_country'])){
			$id_country = $vacancy['id_country'];
		}

		$states = $vacanciesModel->findStates( $id_country );
		$vacancy['id_state'] = empty($vacancy['id_state']) ? '3514450' : $vacancy['id_state'];
		if(!empty($vacancy['id_state'])){
			$id_state = $states[$vacancy['id_state']];
			$cities = $vacanciesModel->findCities( $id_country, $id_state  );
		}
		$countries = $vacanciesModel->findCountries();		
		if($user['roles'][0] == self::COMPANY_USER_ADMIN){
			$id_company = _Company::getCompanyID($user['uid']);
			$users = $this->controller('admin')->companyUsers($id_company);
		}		
		//_Catalogs::pr($cities);		
		$this->view(
			'manager', 
			array(
				'vacancy' => $vacancy, 
				'countries' => $countries, 
				'states' => $states, 
				'cities' => $cities, 
				'rol' => $user['roles'][0],
				'logged_user' => $user['uid'],				
				'users' => $users 
			)
		);	
		
	}

	function save(){
		$data = POST::get('vacancy');
		//_Catalogs::pr($data);
		//die();
		$user = $this->verifyAccess();
		$data['id_company'] = _Company::getCompanyID($user['uid']);
		$data['uid'] = empty($data['uid']) ? $user['uid'] : $data['uid'];

		if(!empty($_FILES)){
			$uploadFiles = $this->Module("uploadFiles");
			
			$error_messages = array(
				1                           => '1. El tamaño del archivo debe ser menor a 1MB',
				2                           => '2. El tamaño del archivo debe ser menor a 1MB',
				3                           => '3. El archivo no termino de cargarse',
				4                           => '4. No se ha podido subir el archivo',
				6                           => '6. No se ha podido subir el archivo',
				7                           => '7. No se ha podido subir el archivo',
				8                           => '8. No se ha podido subir el archivo',
				'post_max_size'             => '9. El tamaño del archivo debe ser menor a 1MB',
				'max_file_size'             => '10. El tamaño del archivo debe ser menor a 1MB',
				'accept_file_types'         => '10. Los tipos de archivos permitidos son: *.pdf, *.doc, *.docx',			
				'abort'                     => '11. No se ha podido subir el archivo',
				'failure_make_folder'       => '12. No se ha podido subir el archivo',
				'failure_register_path'     => '13. No se ha podido subir el archivo',
				'failure_register_file'     => '14. No se ha podido subir el archivo'
			);
			
			$uploadFiles->setErrorMessages($error_messages);
										
			$r_uploadFiles = $uploadFiles->upload(
				$_FILES, //FILES via POST
				$user['uid'],	//uid 
				Config::get('Theme.Web.uploads'), //Directory from uploads 
				'scan/pdfs/', //Constant of upload path  
				array(
					'pdf',
					'doc',
					'docx',
					'jpeg',
					'jpg',
					'bmp',
					'gif',
					'png'
				),	//Extensions allowed 
				'2M', //Max Size
				$uploadFiles::ENCRYPT_WITHOUT_EXT
			);

			$response['files'] = $r_uploadFiles;
			$data['fid'] = !empty($r_uploadFiles[0]['fid']) ? $r_uploadFiles[0]['fid'] : 0;
		}

		$vacanciesModel = $this->model();
		$result = $vacanciesModel->save($data);
		if($result !== false){
			$data['id'] = $result;
			!empty($data['languages']) ? $vacanciesModel->saveLanguages($data) : false;
			!empty($data['industries']) ? $vacanciesModel->saveIndustries($data) : false;
			!empty($data['functions']) ? $vacanciesModel->saveFunctions($data) : false;
		}
		$response['status'] = ($result !== false) ? 1 : 0;
		$response['data'] = $data;		
		echo json_encode($response);
	}


	function languages(){
		//echo '[{"value": "1", "text":"'.$this->get(1).'"}]';
		$vacanciesModel = $this->model();
		$response = $vacanciesModel->findLanguages( $this->get(1) );
		echo json_encode($response);
	}


	function industries(){		
		$vacanciesModel = $this->model();
		$response = $vacanciesModel->findIndustries( $this->get(1) );
		echo json_encode($response);
	}

	function functions(){		
		$vacanciesModel = $this->model();
		$response = $vacanciesModel->findFunctions( $this->get(1) );
		echo json_encode($response);
	}

	function states(){		
		$vacanciesModel = $this->model();
		$id_country = POST::get('id_country');
		$response = $vacanciesModel->findStates( $id_country );
		echo json_encode($response);
	}

	function cities(){		
		$vacanciesModel = $this->model();
		$id_state = POST::get('id_state');
		$id_country = POST::get('id_country');
		$city = POST::get('city');
		$response = $vacanciesModel->findCities( $id_country, $id_state, $city );
		echo json_encode($response);
	}

	public function deleteDocumentVacancy(){

		$response['status'] = 0;
		$post = POST::isReady();		
		if($post){	
			$fid = POST::get('fid');
			if(!empty($fid)){
				$vacanciesModel = $this->model();
				$result = $vacanciesModel->deleteDocumentVacancy( $fid );			
				if($result !== false){
					$response['status'] = 1;
				}
			}
		}
		
		echo json_encode($response);
	}


	function publish(){

		$response['status'] = 0;
		$post = POST::isReady();		
		if($post){	
			$id_vacancy = POST::get('id_vacancy');
			$status = POST::get('status');
			$status = ($status == 'true') ? 0 : 1;			
			if(!empty($id_vacancy)){
				$vacanciesModel = $this->model();
				$result = $vacanciesModel->publish( $id_vacancy, $status );			
				if($result !== false){
					$response['status'] = 1;
				}
			}			
		}
		
		echo json_encode($response);

	}
	
	
	function datail(){
		$vacancy_id = POST::get('vacancy_id');
		$logged = SESSION::get('_SDFGA_5739_AUKK_REFER_');		

		if(is_numeric($vacancy_id)){
			
			$vacanciesModel = $this->model();			
			$revised = POST::get('revised');
			$for_candidates = POST::get('for_candidates');			
			if( $revised == 'true' ){
				$user = $this->verifyAccess();
				$vacanciesModel->revisedByUid($vacancy_id, $user['uid']);	
			}					
			$vacancy = $vacanciesModel->findById($vacancy_id);
			$vacancy['for_candidates'] = !empty($for_candidates) ? 1 : 0;
			if(!empty($for_candidates) && !empty($logged)){
				$user = $this->verifyAccess();
				$vacancy['applied'] = $vacanciesModel->applied($vacancy_id, $user['uid']);
			}
			$this->view('datail', $vacancy);
		}	
	}
	
	function getByUser(){
		$uid = POST::get('uid');	
		if(is_numeric($uid)){
			$params['uid'] = $uid;
			$vacanciesModel = $this->model();
			$users = array();
			$vacancies = $vacanciesModel->findByUser( $uid );			
			if(!empty($vacancies)){
				$id_company = _Company::getCompanyID($uid);
				$users = $this->controller('admin')->companyUsers($id_company);
			}	
			$this->view('assign', array('vacancies' => $vacancies, 'users' => $users, 'uid' => $uid));
		}
	}
	
	function assign( $vacancies, $uid ){		
		$vacanciesModel = $this->model();
		return $vacanciesModel->assign( $vacancies, $uid );				
	}
	
	
	function published(){
										
		//_Catalogs::pr($data);
		$id_company = $this->Get(1);
		if(!empty($id_company) && is_numeric($id_company)){
			$logged = SESSION::get('_SDFGA_5739_AUKK_REFER_');
			$capcha = false;
			$countries = array();
			if(empty($logged)){
				$capchaModule = $this->Module("capcha");
				$capcha = $capchaModule->capchaView("Ingresa el código aquí");
				$vacanciesModel = $this->model();
				$countries = $vacanciesModel->findCountries();
			}
			$company = _Company::allData($id_company);
			$this->view('published', array('company' => $company, 'countries' => $countries, 'capcha' => $capcha));
		}
				
		
	}
	
	
	function published_list(){
		
		$page = POST::get('page');
		$id_company	= POST::get('id_company');
		if(!empty($id_company) && is_numeric($id_company)){	
			$params['page'] = !empty($page) ? $page : 1;
			$params['rows'] = 10;
			$params['id_company'] = 1;
			$logged = SESSION::get('_SDFGA_5739_AUKK_REFER_');
			$_candidate = array();
			if($logged){
				$user = $this->verifyAccess();
				if( $user['roles'][0] == self::CANDIDATE_USER){
					$params['uid'] = $user['uid'];
					$_candidate = $this->controller('candidatos')->getCandidate( $user['uid'] );	
				}
			}
			$vacanciesModel = $this->model();		
			$data = $vacanciesModel->findPublished( $params );
			
			$this->view('published_list', array('vacancies' => $data, 'total_rows' => $data['total_rows'], 'candidate' => $_candidate));
		}
		
	}
	
	
	function applyTo( $vacancy_id = 0, $external = 0, $return = false, $id_candidate = false ){

		$vacancy_id = empty($vacancy_id) ? POST::get('vacancy_id') : $vacancy_id;
		$external = empty($external) ? POST::get('external') : $external;
		$logged = SESSION::get('_SDFGA_5739_AUKK_REFER_');
		$response['success'] = -1;        
        if(!empty($logged)){
			if(!empty($vacancy_id)){
				$user = empty($id_candidate) ? $this->verifyAccess() : array('uid' => $id_candidate, 'roles' => array( 0 => self::CANDIDATE_USER ));
				if( $user['roles'][0] == self::CANDIDATE_USER){
					$data = array(
						'uid' => $user['uid'],
						'vacancy_id' => $vacancy_id,
						'external' => !empty($external) ? 1 : 0
					);
					$vacanciesModel = $this->model();
					$applied = $vacanciesModel->applied($vacancy_id, $user['uid']);
					if(empty($applied)){
						$result = $vacanciesModel->applyTo( $data );
						$applied_date = date('d/m/Y H:i');
					}else{
						$applied_date = date('d/m/Y H:i', strtotime($applied['created']));
						$result = true;
					}					
					if($result !== false){
						$response['data'] = $data;
						$response['success'] = 1;
						$response['msg'] = 'Has aplicado a este empleo<br><i class="fa fa-calendar"></i>&nbsp;&nbsp;'.$applied_date;				
					}else{
						$response['success'] = 0;
						$response['msg'] = 'Error';
					}
				}
			}else{
				$response['success'] = -2;
			}
		}
		
		if($return == false){
			echo json_encode($response);
		}else{
			return $response;	
		}		
		
	}

}

?>