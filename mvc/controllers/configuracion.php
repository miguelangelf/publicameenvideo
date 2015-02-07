<?php

class configuracion extends _controller{
    
    // Public views
    public function index(){ 
		$user = $this->verifyAccess();
		$post = POST::isReady();
		if($post){
			$password = POST::get('password');
			$data['status'] = 0;
			$data['msg'] = 'Ha ocurrido un error';
			$configurationModel = $this->model();
			$result = $configurationModel->changePassword($password['current'], $password['new'], $user['uid']);
			if($result !== false){
				if($result == -1){
					$data['msg'] = 'La contraseña actual es incorrecta';	
				}else{
					$data['status'] = 1;
					$data['msg'] = 'Tu contraseña ha sido actualizada';
				}
			}
			echo json_encode($data);
		}else{
			$data = array('roles' => $user['roles'][0]);
        	$this->view("index", $data ); 
		}
    }
    
    public function editar(){
		$user = $this->verifyAccess();
		$menu = ($this->Get(1) == "exp") ? "exp" : null;
		$post = POST::isReady();
		$configurationModel = $this->model();
		if($post){
			$data['status'] = 0;
			$data['msg'] = 'Ha ocurrido un error';
			$candidate = POST::get('candidate');
			if(!empty($candidate)){
				$candidate['uid'] = $user['uid'];
				$result = $this->controller('registro')->updateCandidate( $candidate );
				if($result !== false) {
					$data['status'] = 1;
					$data['msg'] = 'Tus datos han sido actualizados';
				}
			}
			echo json_encode($data);
		}else{
			$etnias = _Catalogs::etnias();
			$candidato = $this->controller('candidatos')->candidato($user['uid'], true);

			$countries = $configurationModel->findCountries();
			$states = $configurationModel->findStates($candidato["candidate"][$user['uid']]["id_country"]);
			if(!empty($candidato["candidate"][$user['uid']]["id_state"])) {
				$cities = $configurationModel->findCities($candidato["candidate"][$user['uid']]["id_country"],$states[$candidato["candidate"][$user['uid']]["id_state"]]);
			}
 		       
        	$this->view("editar", array('etnias' => $etnias, 'roles' => $user['roles'][0], 'candidato' => $candidato['candidate'][$user['uid']], 'menu' => $menu, 'countries' => $countries, 'states' => $states, 'cities' => $cities)); 
		}
    }

    public function experience() {
		$user = $this->verifyAccess();
		$configurationModel = $this->model();
    	$candidato = $this->controller('candidatos')->candidato($user['uid'], true);

		$countries = $configurationModel->findCountries();
		$key = "MX";
		$countries = array_merge(array($key=>$countries[$key]), $countries);
		$cities = $configurationModel->findCityByCountry($key);
		// $states = $configurationModel->findStates($candidato["candidate"][1]["id_country"]);
		// if(!empty($candidato["candidate"][1]["id_state"])) {
		// 	$cities = $configurationModel->findCities($candidato["candidate"][1]["id_country"],$states[$candidato["candidate"][1]["id_state"]]);
		// 	print_r($cities);
		// }

    	$this->view("experience", array('countries' => $countries, 'cities' => $cities));
    }

    public function curriculum($php = null) {
    	$user = $this->verifyAccess();
    	$post = POST::isReady();
    	if($post) {

    		$tipos = split("/", $_FILES["files"]["type"][0]);

    		if(strpos($tipos[1], "wordprocessingml")) {
    			$tipos[1] = "docx";
    		} else if( $tipos[1] == "msword") {
				$tipos[1] = "doc";
    		}
    		$uploadFiles = $this->Module("uploadFiles");

   //  		if (!is_dir( Config::get('Theme.Web.uploads').$upload_dir)) {
			// 	mkdir( Config::get('Theme.Web.uploads').$upload_dir, '775', true );
			// }
		
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
										
			$response = $uploadFiles->upload(
				$_FILES,
				$user['uid'],
				Config::get('Theme.Web.uploads'),
				'curriculums/', 
				array(
					'pdf',
					'doc',
					'docx',
					'jpeg',
					'jpg',
					'bmp',
					'gif',
					'png'
				),
				'2M',
				$uploadFiles::ENCRYPT_WITHOUT_EXT
			);
			
			if( !empty($response) ) {
				$configurationModel = $this->model();
				if(array_key_exists("file_name", $response[0])) {
					$curriculum = array(
							"name" => $response[0]["file_name"],
							"description" => POST::get("description"),
							"tipo" => $tipos
					);

					$id = POST::get('id');
					if(!empty($id)) {
						$result = $configurationModel->updateDescription((int)POST::get('id'), $curriculum, $response[0]["extension"]);
						$data = array(
								'file' => $response[0]["file_name"],
								'extension' =>  $response[0]["extension"]
						);
						print(json_encode($data));
					} else {
						$result = $configurationModel->addCurriculum($curriculum, $user["uid"], $tipos[1]);
						if(!empty($result)) {
							$data = array(
									'id' => $result,
									'file_name' => $response[0]["file_name"],
									'extension' => $response[0]["extension"]
								);

							print(json_encode($data));
						}
						else print("error");
					}
				} else {
					print_r(json_encode($response));
				}

			}
    	} else {

    		$configurationModel = $this->model();
    		$data = $configurationModel->getCurriculums($user["uid"]);

    		if(empty($php)) $this->view("curriculum", array('curriculums' => $data));
    		else return $data;
    	}
    }

    public function states(){		
		$configurationModel = $this->model();
		$id_country = POST::get('id_country');
		$response = $configurationModel->findStates( $id_country );
		echo json_encode($response);
	}

	public function cities(){		
		$configurationModel = $this->model();
		$id_state = POST::get('id_state');
		$id_country = POST::get('id_country');
		$response = $configurationModel->findCities( $id_country, $id_state );
		echo json_encode($response);
	}

	public function industries(){		
		$vacanciesModel = $this->model();
		$response = $vacanciesModel->findIndustries( $this->get(1) );
		echo json_encode($response);
	}

	public function cityByCountry() {
		$configurationModel = $this->model();
		$id_country = POST::get('id_country');
		$response = $configurationModel->findCityByCountry( $id_country );

		echo json_encode($response);
	}

    public function viewCurriculum() {
    	$file = $this->Module("uploadFiles")->viewFile(Config::get('Theme.Web.uploads'), $this->Get(1));
    }

    public function saveWork() {

    	$user = $this->verifyAccess();
    	$post = POST::isReady();
    	if($post) {

    		$jobArray = POST::get('job');
    		$configurationModel = $this->model();

    		if(!array_key_exists("current", $jobArray)) $jobArray["current"] = 0;
    		else {
    			$jobArray["current"] = 1;
    			$jobArray["endMonth"] = null;
    			$jobArray["endYear"] = null;
    		}

    		$ubicacion = $configurationModel->findCityCountry($jobArray["id_city"]);
    		$ubicacion = $ubicacion[0]["country"].", ".$ubicacion[0]["city"];

    		if(!array_key_exists("id_job", $jobArray))
    			$result = $configurationModel->addJob($user["uid"], $jobArray);
    		else
    			$result = $configurationModel->updateJob($jobArray);
    		
    		$jobArray["location"] = $ubicacion;
    		if($result != 0) {
    			$jobArray["id"] = $result;
    			print(json_encode($jobArray));
    		} else {
    			$jobArray["editado"] = true;
    			print(json_encode($jobArray));
    		}
    	}
	}

	public function deleteWork() {
		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {
    		$configurationModel = $this->model();
    		$result = $configurationModel->deleteJob((int)POST::get('id'));

    		print($result);
    	}
	}

	public function getwork($type = null) {

		$user = $this->verifyAccess();
		$configurationModel = $this->model();
		$result = $configurationModel->selectJobs($user["uid"]);

		foreach ($result as $key => $job) {
			$ubicacion = $configurationModel->findCityCountry($job["id_city"]);
			$ubicacion = $ubicacion[0]["country"].", ".$ubicacion[0]["city"];

			$result[$key]["location"] = $ubicacion;	
		}
		
		$data = json_encode($result);

		if(!empty($type)) {
			return $result;
		} else {
			print($data);
		}
	}

	public function editJob() {
		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {
    		$configurationModel = $this->model();
    		$result = $configurationModel->editJob(POST::get('id_job'));
			
			if(!empty($result))	print(json_encode($result));
    	}
	}

	public function editSchool() {
		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {
    		$configurationModel = $this->model();
    		$result = $configurationModel->editSchool(POST::get('id_school'));
			
			if(!empty($result))	print(json_encode($result));
    	}
	}

	public function editWork() {
		$post = POST::isReady();
    	if($post) {
    		$configurationModel = $this->model();
    		$result = $configurationModel->editJob((int)POST::get('id'));
    	}
	}

	public function editDescription() {

		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {
    		$curriculum = array(
    				"name" => POST::get('file_name'),
    				"description" => POST::get('description')
    			);

    		$configurationModel = $this->model();
    		$result = $configurationModel->updateDescription((int)POST::get('id'), $curriculum);

    		print($result);
    	}
	}

	public function addSchools() {
		$user = $this->verifyAccess();
    	$post = POST::isReady();
    	if($post) {
    		$schoolArray = POST::get('school');
    		if(!array_key_exists("id_school", $schoolArray)) {
    			$configurationModel = $this->model();
	    		$result = $configurationModel->addSchool($user["uid"], $schoolArray);
	    		if($result != 0) $schoolArray["id"] = $result;
	    		
	    		print(json_encode($schoolArray));
    		} else {
    			$configurationModel = $this->model();
	    		$result = $configurationModel->updateSchool($schoolArray);
	    		$schoolArray["editar"] = true;

	    		print(json_encode($schoolArray));
    		}
    	}
	}

	public function getSchools($type = null) {
		$user = $this->verifyAccess();
    	$configurationModel = $this->model();
    	$result = $configurationModel->selectSchools($user["uid"]);

    	if(!empty($type)) {
			return $result;
		} else {
			print_r(json_encode($result));
		}
   
	}

	public function deleteSchool() {
		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {
    		$configurationModel = $this->model();
    		$result = $configurationModel->deleteSchool((int)POST::get('id'));

    		print($result);
    	}
	}

	public function deleteCurriculum() {
		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {

    		$configurationModel = $this->model();
    		$result = $configurationModel->deleteCurriculum((int)POST::get('id'));
    		
    		if($result) {
    			unlink(Config::get('Theme.Web.uploads')."curriculums/".POST::get('name'));
    			print($result);
    		}
    	}
	}

	public function saveSL() {
		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {
    		$skill = POST::get('skill');
    		$language = POST::get('language');
    		$index = POST::get('id');
    		$configurationModel = $this->model();

    		if(!empty($skill)) {
    			$skillList = array();
    			$skillActual = json_decode($configurationModel->getSL("skills", $user["uid"]));

    			if(!empty($skillActual)) {
    				if(isset($index)) $skillActual[$index] = $skill;
    				else $skillList[] = $skill;
    				$skillList = array_values(array_merge($skillActual, $skillList));
    			}
    			else $skillList[] = $skill;

    			$result = $configurationModel->saveSL(json_encode($skillList), null, $user["uid"]);
    			if($result == 0) {
    				end($skillList);
					$key = key($skillList);
					print($key);
    			}
    		} else if(!empty($language)) {
    			$languageList = array();
    			$languageActual = json_decode($configurationModel->getSL("languages", $user["uid"]));
				
				if(!empty($languageActual)) {
					if(isset($index)) $languageActual[$index] = $language;
    				else $languageList[] = $language;
					$languageList = array_values(array_merge($languageActual, $languageList));
				} 
				else $languageList[] = $language;

    			$result = $configurationModel->saveSL(null, json_encode($languageList), $user["uid"]);
    			if($result == 0) {
    				end($languageList);
					$key = key($languageList);
					print($key);
    			}
    		}
    	}
	}

	public function removeSL() {
		$user = $this->verifyAccess();
		$post = POST::isReady();
    	if($post) {
    		$skill = POST::get('skill');
    		$language = POST::get('language');
    		$configurationModel = $this->model();

    		if(!empty($skill)) {
    			$skillActual = json_decode($configurationModel->getSL("skills", $user["uid"]));
    			if(!empty($skillActual)) {
    				$key = array_search((object)$skill, $skillActual);
    				unset($skillActual[$key]);
    				$skillActual = array_values($skillActual);
    				$result = $configurationModel->saveSL(json_encode($skillActual), null, $user["uid"]);
    				print($key);
    			}
    		} else if(!empty($language)) {
    			$languageActual = json_decode($configurationModel->getSL("languages", $user["uid"]));
    			if(!empty($languageActual)) {
    				$key = array_search((object)$language, $languageActual);
    				unset($languageActual[$key]);
    				$languageActual = array_values($languageActual);
    				$result = $configurationModel->saveSL(null, json_encode($languageActual), $user["uid"]);
    				print($key);
    			}
    		}
    	}
	}

	public function skills() {
		$user = $this->verifyAccess();
		$configurationModel = $this->model();

		$skills = json_decode($configurationModel->getSL("skills", $user["uid"]));

		$this->view("skills", array('skills' => $skills));
	}

	public function languages() {
		$user = $this->verifyAccess();
		$configurationModel = $this->model();

		$languages = json_decode($configurationModel->getSL("languages", $user["uid"]));

		$this->view("languages", array('languages' => $languages));
	}

	public function getSLs() {
		$user = $this->verifyAccess();
		$configurationModel = $this->model();

		$languages = json_decode($configurationModel->getSL("languages", $user["uid"]));
		$skills = json_decode($configurationModel->getSL("skills", $user["uid"]));

		$data["languages"] = $languages;
		$data["skills"] = $skills;

		return $data;
	}
	
	
	function saveApplyCV( $data ){
		
		$curriculum = array(
				"name" => $data["file_name"],
				"description" => 'curriculum'
		);
		
		$configurationModel = $this->model();
		$id = $configurationModel->addCurriculum($curriculum, $data["uid"], $data["extension"]);
		return $id;
	}

    
}

?>