<?php

class admin extends _controller{
	
	const COMPANY_USER = 5;
	const CANDIDATE_USER = 4;
	const GOVERNMENT_USER = 6;
	const ADMIN_USER = 7;
	const COMPANY_USER_ADMIN = 8;
    
    // Public views
    public function index(){
        $data = array();
        $this->view("index", $data); 
    }
    
    public function sliderManager(){
        $data = array();
        $this->view("sliderManager", $data);         
    }
		
	public function inicio(){
		$user = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0], 'home' => 'open');
        $this->view("inicio", $data);         
    }	
	
	public function empresa(){
        $user = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0], 'empresa' => 'open');
        $this->view("empresa", $data);         
    }
	
	public function reportes(){
        $user = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0], 'reportes' => 'open');
        $this->view("reportes", $data);         
    }
	
	public function contenido(){
		$user = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0]);
        $this->view("contenido", $data);         
    }
	
	public function main_slider(){
		$user = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0]);
        $this->view("main_slider", $data);         
    }
	
	public function companies_slider(){
		$user = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0]);
        $this->view("companies_slider", $data);         
    }
	
	function form_company(){
		$data = array();	
		$id_company = POST::get('id_company');	
		if(!empty($id_company)){			
			$adminCompany = $this->model();
			$company = $adminCompany->findCompany($id_company);
			$data = !empty($company) ? $company['company'][0] : array();						
		}	                
                $sectors =  _Catalogs::sectors();
                $data['sectors'] = $sectors;
		$this->view("form_company", $data);
	}
	
	function form_office(){
		$data = array();
		$id_office = POST::get('id_office');
		if(!empty($id_office)){
			$adminOffice = $this->model();
			$office = $adminOffice->findOffices($id_office);
			$data = !empty($office) ? $office['office'][0] : array();
		}		
		$this->view("form_office", $data);
	}
	
	function list_companies(){				
		
		$params['rows'] = 10;		
		if($this->Get(1) == 'page'){
			$param = $this->Get(2);
			$params['page'] = (!empty($param) && is_numeric($param)) ? $param : 1;
		}
		
		$findByName = POST::get('_search');
		!empty($findByName) ? ($params['name'] = $findByName) : false;
		
		$findByStatus = POST::get('_status');
		($findByStatus != NULL) ? ($params['status'] = $findByStatus) : false;
						
		$adminCompany = $this->model();
		$data = $adminCompany->findCompany( false, $params );
		
		if(!empty($data)){
			$pages =  Paginator::pagination(array(
				'total_rows' => $data['total_rows'], 
				'url' => Config::get("Core.Domains.core").'web/admin/list_companies/',
				'current_page' => $params['page'],
				'page_rows' => $params['rows'],
				'max_pages' => 3,
				'next' => '>',
				'first' => '<<',			
				'last' => '>>',
				'previous' => '<',
				'info' => '%total_rows% Compañia(s)'				
				)
			);		
		
			/*echo '<pre>';
			print_r($data['company']);
			echo '</pre>';*/
							
			$this->view("list_companies", array('company' => $data['company'], 'pagination' => $pages));
		}
	}
	
	function list_offices(){				
		
		$params['rows'] = 10;		
		if($this->Get(1) == 'page'){
			$param = $this->Get(2);
			$params['page'] = (!empty($param) && is_numeric($param)) ? $param : 1;
		}
		
		$findByName = POST::get('_search');
		!empty($findByName) ? ($params['name'] = $findByName) : false;
				
		$adminOffice = $this->model();
		$data = $adminOffice->findOffices( false, $params );		
		
		if(!empty($data)){
			$pages =  Paginator::pagination(array(
				'total_rows' => $data['total_rows'], 
				'url' => Config::get("Core.Domains.core").'web/admin/list_offices/',
				'current_page' => $params['page'],
				'page_rows' => $params['rows'],
				'max_pages' => 3,
				'next' => '>',
				'first' => '<<',			
				'last' => '>>',
				'previous' => '<',
				'info' => '%total_rows% Enlace(s)'				
				)
			);
			
			$this->view("list_offices", array('office' => $data['office'], 'pagination' => $pages));
		}
								
	}
	
	function saveOffice(){
		
		$office = POST::get('office');
		$adminOffice = $this->model();
		$result = $adminOffice->saveOffice($office);
		if($result !== false){
			$response['msg'] = "El Enlace ha sido registrado";
			$response['status'] = 1;
		}else{
			$response['msg'] = "Ha ocurrido un problema, favor de intentarlo nuevamente";
			$response['status'] = 0;	
		}
		
		echo json_encode($response);
		
	}
	
	function dataCompany( $id_comapny ){
		$adminCompany = $this->model();
		return $adminCompany->findCompany($id_comapny);
	}
	
	function saveCompany(){
		$post = POST::isReady();
		if($post){
			$company = POST::get('company');
			if(!empty($company)){
				
				if(!empty($company['picture_base64'])){
					$picture_base64 = str_replace('data:image/png;base64,', '', $company['picture_base64']);
					$image = base64_decode($picture_base64);
					$upload_dir = 'companies_pictures';
					if($image !== false){
						if (!is_dir( Config::get('Theme.Web.uploads').$upload_dir)) {
							mkdir( Config::get('Theme.Web.uploads').$upload_dir, '775', true );
						}
						$file_name = $upload_dir.'/'.$company['id'].'.png';
						$fp = @fopen( Config::get('Theme.Web.uploads').$file_name, 'w');
						if($fp !== false){
							$fwrite = fwrite($fp, $image);
							if($fwrite !== false){
								$company['logo'] = $file_name;
							}else{
								$response['msg'] = '3. Archivo invalido';
								$image_save = false;
							}
							fclose($fp);
						}else{
							$response['msg'] = '2. Archivo invalido ';
							$image_save = false;
						}
					}else{
						$response['msg'] = '1. Archivo invalido';
						$image_save = false;
					}
				}
				
				$adminCompany = $this->model();
				$result = $adminCompany->saveCompany($company);
				if($result !== false){
					$response['msg'] = "La información ha sido guardada";
					$response['status'] = 1;
					is_numeric($result) ? $result['id'] : true;
				}else{
					$response['msg'] = "Ha ocurrido un problema, favor de intentarlo nuevamente";
					$response['status'] = 0;	
				}
			}
			echo json_encode($response);
		}
	}
	
	
    public function users(){
		$user = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0], 'users' => 'open');
        $this->view("users", $data); 
    }			
	
	public function list_users(){
		
		$conditions['pagination']['rows'] = 8;
		$conditions['pagination']['page'] = 1;		
		if($this->Get(1) == 'page'){
			$page = $this->Get(2);
			$conditions['pagination']['page'] = (!empty($page) && is_numeric($page)) ? $page : 1;			
		}		
		
		$findByName = POST::get('_search');
		!empty($findByName) ? ($conditions['name'] = $findByName) : false;
				
		$adminUsers = $this->model();
		$data = $adminUsers->findUsers($conditions);
		
		if(!empty($data)){
			$pages =  Paginator::pagination(array(
				'total_rows' => $data['total_rows'], 
				'url' => Config::get("Core.Domains.core").'web/admin/list_users/',
				'current_page' =>$conditions['pagination']['page'],
				'page_rows' => $conditions['pagination']['rows'],
				'max_pages' => 3,
				'next' => '>',
				'first' => '<<',			
				'last' => '>>',
				'previous' => '<',
				'info' => '%total_rows% Usuario(s)'				
				)
			);
			
			$this->view("list_users", array('users' => $data['users'], 'pagination' => $pages));
			//_Catalogs::pr($data['users']);
		}								
			
	}
	
	function form_user(){
		
		$data = array();		
		$id_user = POST::get('id_user');
		
		if(!empty($id_user)){
			$adminUsers = $this->model();
			$result = $adminUsers->findUsers($id_user);
			$data = $result['users'][0];
		}
		
        $this->view("form_user", $data);
	}	
	
	
	function catalog(){
		$data = array();
		$adminUsers = $this->model();
		$list_of = POST::get('list_of');
		switch($list_of){
			case self::COMPANY_USER_ADMIN:
			case self::COMPANY_USER:
				$result = $adminUsers->findCompany( false, array('status' => 1));
				if(!empty($result['company'])){
					$data = $result['company'];
				}
			break;
			case self::GOVERNMENT_USER:
				$result = $adminUsers->findOffices();
				if(!empty($result['office'])){
					$data = $result['office'];
				}
			break;
		}
		echo json_encode($data);
	}
	
	
	function saveUser(){		
		$data['status'] = 0;
		$data['msg'] = '1. Existio un error';		
		$post = POST::isReady();
		if($post){
			$user = POST::get('user');									
			switch($user['rid']){
				case self::COMPANY_USER_ADMIN:
				case self::COMPANY_USER:							
					$data = $this->addCompanyUser($user);
				break;
				case self::GOVERNMENT_USER:
					$data = $this->addGovernmentUser($user);					
				break;
				case self::ADMIN_USER:
					$data = $this->addAdminUser($user);
				break;					
			}			
		}
		if($data['status'] == 1){
			if(!empty($data['uid'])){
				 $dataUser = $this->controller('registro')->getCandidateToken( $data['uid'] );
				 if(!empty($dataUser['token'])){
					$user['token'] = $dataUser['token'];
					$body = utf8_decode(_twig('mails/activate.twig', $user));
					$mail = new Mail("default", 'Confirma tu cuenta', $body);
					$mail->addAddress($user['email'], $user['name'].' '.$user['last_name']);
					$mail->send();
				}
				unset($data['uid']);
			}
		}
		echo json_encode($data);
		
	}
	
	
	function addCompanyUser($user){
		$data['status'] = 0;
		$data['msg'] = '4. Existio un error';
		if(empty($user['uid'])){			
			$data = $this->addUser($user);
			if(is_numeric($data)){
				$user['uid'] = $data;
				$user['id_company'] = $user['assoc_id'];								
				$result = $this->controller('registro')->dataCompanyUser( $user );
				if($result !== false){
					$response['uid'] = $user['uid'];
					$response['status'] = 1;
					$response['msg'] = 'El usuario ha sido creado';
					return $response;
				}
			}else{			
				return $data;
			}
		}else{
			$user['id_company'] = $user['assoc_id'];
			$user['id_company_original'] = $user['original_assoc_id'];
			$adminModel = $this->model();
			if(!empty($user['id_company_original'])){			
				$result = $adminModel->updateCompanyUser( $user );						
			}else{
				$result = $this->controller('registro')->dataCompanyUser( $user );
				if($result != false){
					$result = $adminModel->updateUser($user);	
				}
			}
			if($result != false){
				$data['status'] = 1;
				$data['msg'] = 'El usuario ha sido actualizado';	
			}
			return $data;	
		}
		
	}
	
	private function addGovernmentUser($user){
		$data['status'] = 0;
		$data['msg'] = '4. Existio un error';
		if(empty($user['uid'])){
			$data = $this->addUser($user);
			if(is_numeric($data)){
				$user['uid'] = $data;
				$user['id_office'] = $user['assoc_id'];				
				$adminModel = $this->model();
				$result = $adminModel->dataGovernmentUser( $user );
				if($result !== false){
					$response['uid'] = $user['uid'];
					$response['status'] = 1;
					$response['msg'] = 'El usuario ha sido creado';
					return $response;
				}
			}else{			
				return $data;
			}
		}else{
			$user['id_office'] = $user['assoc_id'];
			$adminModel = $this->model();
			if(!empty($user['original_assoc_id'])){
				$result = $adminModel->updateGovernmentUser( $user );			
			}else{
				$result = $adminModel->dataGovernmentUser( $user );
				if($result != false){
					$result = $adminModel->updateUser($user);	
				}	
			}
			if($result != false){
				$data['status'] = 1;
				$data['msg'] = 'El usuario ha sido actualizado';	
			}
			return $data;	
		}	
	}
	
	private function addAdminUser($user){
		$data['status'] = 0;
		$data['msg'] = '4. Existio un error';
		if(empty($user['uid'])){
			$data = $this->addUser($user);
			if(is_numeric($data)){
				$response['uid'] = $data;
				$response['status'] = 1;
				$response['msg'] = 'El usuario ha sido creado';
				return $response;
			}else{			
				return $data;
			}
		}else{
			$adminModel = $this->model();
			$result = $adminModel->updateUser($user);
			if($result != false){
				$data['status'] = 1;
				$data['msg'] = 'El usuario ha sido actualizado';	
			}
			return $data;
		}
	}
	
	private function addUser( $user )	{
		
		$data['status'] = 0;
		$data['msg'] = '2. Existio un error';		
		$user['password'] = '';		
		$user['rol'] = $user['rid'];
		if(!empty($user['email'])){
			$uid = Users::add($user);		
			
			if(is_numeric($uid)){			
				if($uid > 0){
					$adminModel = $this->model();
					$adminModel->setStatus($uid);
					return $uid;                                    
				}else{						
				   $data['msg'] = 'Ya existe un usuario registrado con este email';                                                			   
				}			
			}else{
				$response['msg'] = '3. Existio un error';
			}
		}		
		
		return $data;
	}
	
	
	function activate(){
		$token = $this->Get(1);
		if(!empty($token)){
			$data = $this->controller('registro')->getCandidateByToken( $token );
			if(!empty($data['uid'])){
				$post = POST::isReady();
				$response = array();
                                
                                $adminModel = $this->model();
                                $us_rol = $adminModel->getRoles($data['uid']);

                                if($data['id_status'] == 0){
                                    echo "Acceso denegado. La cuenta no ha sido activada";
                                    return false;
                                }
                                                                
                                if($post){                                                                                        

                                    $user = POST::get('user');
                                    $valid = Validator::validate(
                                            $user['pass'], 
                                            array(
                                                    'REQUIRED', 
                                                    array(
                                                            'MIN_STRLEN' => 0
                                                    )
                                            ), 
                                            array(
                                                    'MIN_STRLEN' => 'La contraseña debe ser mayor a 8 caracteres',
                                                    'REQUIRED' => 'No has introduciodo una contraseña'
                                            )
                                    );

                                    if(empty($valid)){						
                                            if($user['pass'] == $user['confirm_pass']){
                                                    $enc_password = Users::setPassword($data['uid'], $data['email'], $user['pass']);
                                                    if($enc_password !== false){                                                                
                                                            if($us_rol != self::CANDIDATE_USER){
                                                                $this->controller('registro')->activateCandidate($data['uid']); 
                                                            }                                                                
                                                            Users::autoLogin($data['uid'], $enc_password);
                                                            $response['success'] = 'Tu contraseña se ha actualizado';                                                                                                                                                                                                
                                                    }else{
                                                            $response['error'] = 'Ha ocurrido un error';
                                                    }
                                            }else{
                                                    $error = 'Las contraseñas no son iguales';
                                                    $response['error'] = $error;	
                                            }
                                    }else{
                                            $error = !empty($valid['REQUIRED']['msg']) ? $valid['REQUIRED']['msg'] : $valid['MIN_STRLEN']['msg'];
                                            $response['error'] = $error;	
                                    }

                                }
                                
				
				$this->view('activate', $response);
			}else{
				echo "Acceso denegado.";
			}
		}else{
			echo "Acceso denegado.";
		}
	}
	
	
	function disableUser(){
		
		$post = POST::isReady();
		if($post){			
			$user = POST::get('user');
			$uid = $user['uid'];			
			$data['status'] = 0;
			$data['msg'] = 'Ha ocurrido un error';
			if(!empty($uid)){			
				$adminModel = $this->model();
				$result = $adminModel->disableUser( $uid );
				if($result != false){
					switch($user['original_rid']){
						case self::COMPANY_USER:
						case self::COMPANY_USER_ADMIN:
							$adminModel->inactiveCompanyuser( $uid );
						break;
					}
					$data['status'] = 1;
					$data['msg'] = 'El usuario ha sido eliminado';	
				}
			}
			echo json_encode($data);
		}
		
	}
	
	
	function disableCompany(){
		
		$post = POST::isReady();
		if($post){			
			$id_company = POST::get('id_company');
			$data['status'] = 0;
			$data['msg'] = 'Ha ocurrido un error';
			if(!empty($id_company)){			
				$adminModel = $this->model();
				$result = $adminModel->disableCompany( $id_company );
				if($result != false){
					$data['status'] = 1;
					$data['msg'] = 'La empresa ha sido eliminada';	
				}
			}
			echo json_encode($data);
		}
		
	}
	
	
	function disableOffice(){
		
		$post = POST::isReady();
		if($post){			
			$id_office = POST::get('id_office');
			$data['status'] = 0;
			$data['msg'] = 'Ha ocurrido un error';
			if(!empty($id_office)){			
				$adminModel = $this->model();
				$result = $adminModel->disableOffice( $id_office );
				if($result != false){
					$data['status'] = 1;
					$data['msg'] = 'El enlace ha sido eliminado';	
				}
			}
			echo json_encode($data);
		}
		
	}
	
	
	function statusCompany(){
		
		$post = POST::isReady();
		if($post){			
			$id_company = POST::get('id_company');
			$status = POST::get('status');
			$data['status'] = 0;
			if(!empty($id_company)){
				$adminModel = $this->model();
				$result = $adminModel->statusCompany( $id_company, $status);
				if($result != false){
					$data['status'] = 1;						
				}
			}			
			echo json_encode($data);
		}				
			
	}
	
	
	function managerTimeSlots(){
		
		$post = POST::isReady();
		if($post){
			$id_office = POST::get('id_office');
			if(!empty($id_office) && is_numeric($id_office)){
				$adminModel = $this->model();
				//$data['list_offices'] = $adminModel->findOffices();				
				$data['office'] = $adminModel->getTimeSlots($id_office);				
				$data['id_office'] = $id_office;
			}
			$this->view('managerTimeSlots', $data);
		}
		
	}
	
	
	function getTimeSlots(){
		$post = POST::isReady();
		if($post){
			$id_office = POST::get('id_office');	
			if(!empty($id_office) && is_numeric($id_office)){
				$adminModel = $this->model();
				$data['office'] = $adminModel->getTimeSlots($id_office);
				$this->view('list_time_slots', $data);
			}
		}
	}
	
	
	function timeSlots(){
		$post = POST::isReady();
		if($post){
			$time = POST::get('time');
			$response['status'] = 0;
			$response['msg'] = 'Ha ocurrido un error.';
			
			$adminModel = $this->model();
			$result = $adminModel->timeSlots($time);
			if($result !== false){
				$response['status'] = 1;
				$response['msg'] = 'Se han credo los registros';
			}			
			
			echo json_encode($response);
		}
	}
	
	
	function updateCompanyData(){
		$user = $this->verifyAccess();
		$post = POST::isReady();
		if($post){
			$id_company = _Company::getCompanyID($user['uid']);
			$adminCompany = $this->model();
			if(!empty($id_company)){
				$section = POST::get('section');
				switch($section){
					case 'companyGeneralData':
						$sectors =  _Catalogs::sectors();
						$adminCompany = $this->model();
						$company = $adminCompany->findCompany($id_company);
						$data = !empty($company) ? $company['company'][0] : array();	
						$data['sectors'] = $sectors;						
					break;
					case 'companyAddressData':
						$adminCompany = $this->model();
						$company = $adminCompany->findCompany($id_company);
						$data = !empty($company) ? $company['company'][0] : array();
					break;
					case 'companyDirectorData':
						$adminCompany = $this->model();
						$company = $adminCompany->findCompany($id_company);
						$data = !empty($company) ? $company['company'][0] : array();
					break;
					case 'companyProfileData':
						$adminCompany = $this->model();
						$company = $adminCompany->findCompany($id_company);
						$data = !empty($company) ? $company['company'][0] : array();
					break;
					case 'userProfile':
						$adminCompany = $this->model();
						$data = $adminCompany->getInfoUser($user['uid'], self::COMPANY_USER);
						//_Catalogs::pr($data);
					break; 
					case 'companyCareerSite':
						$data = _Company::allData( $id_company );
						//_Catalogs::pr($data);
					break;
				}
				if(!empty($data)){
					$this->view($section, $data);	
				}
			}
		}
	}
	
	
	function dias_festivos(){
		$post = POST::isReady();
		if($post){			
			$adminModel = $this->model();
			$data = $adminModel->findOffices();
			$data['times']= $adminModel->getTimeSlots();
			$data['id_office'] = POST::get('id_office');
			$holyday = POST::get('holiday');
			if(!empty($holyday)){
				$data['day'] = substr($holyday, 8, 2);
				$data['month'] = substr($holyday, 5, 2);
				$data['year'] = substr($holyday, 0, 4);
			}
			$this->view('dias_festivos', $data );
		}
	}	
	
	
	function officeHoliDays(){
		
		$post = POST::isReady();
		if($post){
			$params = POST::get('holiday');
			$data = array();
			if(!empty($params['year']) && !empty($params['month']) && !empty($params['day']) && is_numeric($params['id_office'])){													
				$adminModel = $this->model();
				$data = $adminModel->findOfficesHoliDays( $params );			
			}
			echo json_encode( $data );
		}
	}
	
	
	function saveHoliDays(){
		
		$post = POST::isReady();
		if($post){
			$response['status'] = 0;
			$response['msg'] = 'Ha ocurrido un error.';
			$holiday = POST::get('holiday');
			
			if(empty($holiday['year']) || empty($holiday['month']) || empty($holiday['day'])){
				$response['msg'] = 'Selecciona una fecha valida';
			}else{			
				$adminModel = $this->model();
				$result = $adminModel->saveHoliDays( $holiday );
				if($result !== false){
					$response['status'] = 1;
					$response['msg'] = 'Se han credo los registros';
				}		
			}
			echo json_encode( $response );
		}
		
	}
	
	function getHoliDays(){
		$post = POST::isReady();
		if($post){			
			$id_office = POST::get('id_office');
			$adminModel = $this->model();
			$data = $adminModel->findOfficesHoliDays( array('id_office' => $id_office ) );
			$this->view('list_holidays', $data );	
		}			
	}


	function generalTimeSlots(){
		$adminModel = $this->model();
		$data = $adminModel->getTimeSlots();
		$this->view('generalTimeSlots', $data);		
	}


	function manageSlot(){
		$slot = POST::get('slot');
		$adminModel = $this->model();
		$data = $adminModel->manageSlot($slot);
		$response['status'] = 0;
		echo json_encode($response);
	}
        
        
        function getCPS() {
            $post = POST::isReady();
            if ($post) {
                $id_office = POST::get('id_office');
                $adminModel = $this->model();
		$data['cps'] = $adminModel->getCPS($id_office);                
                $data['id_office'] = $id_office;
                $this->view('list_cps', $data);
            }
        }
        
        
        function saveCPS() {
            
            $post = POST::isReady();
            if ($post) {
                $office_cps = POST::get('office_cps');
                $response['status'] = 0;
                $response['msg'] = 'Ha ocurrido un error.';                    
                    if(!empty($office_cps['cp'])){
                        $adminModel = $this->model();
                        $result = $adminModel->saveOfficeCPS( $office_cps );
                        if($result !== false){
                            $response['status'] = 1;
                            $response['msg'] = 'Se ha guardado correctamente';
                        }		
                    }
                    echo json_encode( $response );
            }
            
        }

        function companyUsers( $companies = false, $params = false){
            $post = POST::isReady();
            if ($post) {
            	if(!empty($companies)){
                    $adminModel = $this->model();
                    return $adminModel->companyUsers( $companies, $params );	
            	}else{
                    $id_company = POST::get('id_company');
                    if(!empty($id_company)){
            		$adminModel = $this->model();
                        $result = $adminModel->companyUsers( $id_company );
                        $this->view('users_companies', array('users' => $result));                    
                    }
                }	
            }
        }


    function verifyEmail(){

    	$token = $this->Get(1);
    	if(empty($token)){
    		echo "Acceso denegado.";
    		return false;	
    	}

		$data = $this->controller('registro')->getCandidateByToken( $token );
		if(empty($data['uid'])){
			echo "Acceso denegado.";
    		return false;	
		}

		$result = $this->controller('registro')->activateCandidate($data['uid']);

		if($result !== false){
			Users::autoLogin($data['uid'], $data['password']);			
			header('Location: '.Config::get("Core.Domains.core").'web/acceso/redirect'); 
		}
		

    }
	
	
	function saveCompanyUser(){
		
		$user = $this->verifyAccess();
		$data = POST::get('User');
		$data['rid'] = $user['roles'][0];		
		$response['data'] = $data;
		$response['status'] = 0;
		$response['msg'] = 'Ha ocurrido un error.';
		$image_save = true;
		
		if(!empty($data)){
						
			if(!empty($data['picture_base64'])){
				$picture_base64 = str_replace('data:image/png;base64,', '', $data['picture_base64']);
				$image = base64_decode($picture_base64);
				$upload_dir = 'users_companies_picture';
				if($image !== false){
					if (!is_dir( Config::get('Theme.Web.uploads').$upload_dir)) {
						mkdir( Config::get('Theme.Web.uploads').$upload_dir, '775', true );
					}
					$file_name = $upload_dir.'/'.$data['uid'].'.png';
					$fp = @fopen( Config::get('Theme.Web.uploads').$file_name, 'w');
					if($fp !== false){
						$fwrite = fwrite($fp, $image);
						if($fwrite !== false){
							$data['picture']	= $file_name;
						}else{
							$response['msg'] = '3. Archivo invalido';
							$image_save = false;
						}
						fclose($fp);
					}else{
						$response['msg'] = '2. Archivo invalido ';
						$image_save = false;
					}
				}else{
					$response['msg'] = '1. Archivo invalido';
					$image_save = false;
				}
			}
			
			$adminModel = $this->model();
            $result = $adminModel->updateCompanyUser( $data );
			if($result !== false && $image_save !== false){ 
				$response['status'] = 1;
				$response['msg'] = 'Tu perfil ha sido guardado.'; 
			}
			
			echo json_encode($response);
		}
	}
	
	
	function companyUserPicture(){
		$uid = $this->get(1);
		$file_name =  Config::get('Theme.Web.uploads').'users_companies_picture/'.$uid.'.png';
		if (file_exists( $file_name )) {
			readfile( $file_name ); 
		}else{
			readfile( Config::get('Core.Domains.theme')."images/default-user-picture.png" );
		}
	}
	
	function companyPicture(){
		$uid = $this->get(1);
		$file_name =  Config::get('Theme.Web.uploads').'companies_pictures/'.$uid.'.png';
		if (file_exists( $file_name )) {
			readfile( $file_name ); 
		}else{
			readfile( Config::get('Core.Domains.theme')."images/logo_default.png" );
		}
	}
	
	function companySettingsForm(){
		$id_company = POST::get('id_company');
		if(!empty($id_company) && is_numeric($id_company)){
			$data = _Company::allData($id_company);
			$this->view('companySettingsForm', $data );
		}
	}
	
	
	function saveCompanySettings(){
		$post = POST::isReady();
        if ($post) {
			$data = POST::get('company');
			$response['status'] = 0;
			$adminModel = $this->model();			
			$result = $adminModel->saveCompany( $data );
			$response['data'] = $data;
			if($result !== false){ 
				$response['status'] = 1;
				$response['msg'] = 'Se ha guardado correctamente';
			}
				
			echo json_encode($response);
		}
	}

	
}

?>