<?php
class perfiles_companias extends _controller{
	
    public function index(){	
        $data["demo1"] = $this->Module("demo1")->demo();
        $model = $this->model();
        $data['rsCompanies'] = $model->getActiveCompanies();
        $this->view("index", $data); 
    }

    public function detalle_compania(){	
        $data["demo1"] = $this->Module("demo1")->demo();
        $model = $this->model();
        $id = $this->Get(1);
        $data["rsCompanyDetail"] = $model->getCompanyProfile($id);
        $this->view("detalle_compania", $data); 
    }
	
	public function sitios_de_empleo(){
		$data["demo1"] = $this->Module("demo1")->demo();
		$model = $this->model();
        $data['rsCompanies'] = $model->getCareerSitesCompanies();
		$this->view("sitios_de_empleo", $data); 
	}
}
?>