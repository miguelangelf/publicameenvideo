<?php

class equipo extends _controller{
	
	const COMPANY_USER = 5;
	const COMPANY_USER_ADMIN = 8;
    
    // Public views
    public function index(){
		
		$user = $this->verifyAccess();
		$params['page'] = 1;
		if($this->Get(1) == 'page'){
			$param = $this->Get(2);
			$params['page'] = (!empty($param) && is_numeric($param)) ? $param : 1;
		}
		$params['rows'] = 10;		
		$id_company = _Company::getCompanyID($user['uid']);	 
		$users = $this->controller('admin')->companyUsers( $id_company, $params );
		$_Company = _Company::allData($id_company, 'available_users');
		//_Catalogs::pr($users);
		
		$pages =  Paginator::pagination(
			array(
				'total_rows' => $users['total_rows'], 
				'url' => Config::get("Core.Domains.core").'empresa/equipo/index/',
				'current_page' => $params['page'],
				'page_rows' => $params['rows'],
				'max_pages' => 5,
				'next' => '>',
				'first' => '<<',			
				'last' => '>>',
				'previous' => '<',
				'info' => '%total_rows% Usuario(s) en total'
				//'Page %page% of %pages%, showing %page_rows% records out of %total_rows% total, starting on record %start%, ending on %end%'			
			)
		);		
		
        $this->view(
			"index", 
			array(
				'pagination' => $pages, 
				'users' => $users['rows'], 
				'total_rows' => $users['total_rows'], 
				'role' => $user['roles'][0], 
				'available_users' => $_Company['available_users'],
				'logged_user' => $user['uid']
			)
		); 
		
    }
	
	function manager(){
		$this->view("manager");	
	}
	
	function saveUser(){		
		$user = $this->verifyAccess();
		$id_company = _Company::getCompanyID($user['uid']);		
		$data = POST::get('user');
		$data['assoc_id'] = $id_company;
		$users = $this->controller('admin')->companyUsers( $id_company );
		$num_users = count($users);
		$_Company = _Company::allData($id_company, 'available_users');
		$data['rid'] = self::COMPANY_USER;
		if($user['roles'][0] == self::COMPANY_USER_ADMIN && $_Company['available_users'] > $num_users ){
			$response = $this->controller('admin')->addCompanyUser( $data );
			if($response['status'] == 1){
				if(!empty($response['uid'])){
					 $dataUser = $this->controller('registro')->getCandidateToken( $response['uid'] );
					 if(!empty($dataUser['token'])){
						$data['token'] = $dataUser['token'];
						$body = utf8_decode(_twig('mails/activate.twig', $data));
						$mail = new Mail("default", 'Confirma tu cuenta', $body);
						$mail->addAddress($data['email'], $data['name'].' '.$data['last_name']);
						$mail->send();
					}					
				}
			}
		}else{
			$response['status'] = 0;
			$response['msg'] = 'Error'.($_Company['available_users'] <= $num_users ? ': No puedes crear mas usuarios.' : '');
		}
		echo json_encode($response);
	}
	
	function disableUser(){
		$data = POST::get('vacancies');
		if(!empty($data['ids']) && !empty($data['uid'])){
			$this->controller('vacancies')->assign( $data['ids'], $data['uid'] );	
		}
		$this->controller('admin')->disableUser();
	}
		
}

?>