<?php

class busquedas extends _controller{
    
    // Views
    public function index(){
        $user       = $this->verifyAccess();
        $data = array('roles' => $user['roles'][0], 'searches' => 'open');	
        $companyID  = _Company::getCompanyID($user["uid"]);
        $this->Module("filters")->setOwner($companyID);
        $data["ModuleFilters"]["Admin"] = $this->Module("filters")->_admin();
        $this->view("index", $data);
    }
    
    public function getcities(){
        $_ = json_encode(_Catalogs::cities());
        echo $_;
    }
    
    public function getstudies(){
        $_ = json_encode(_Catalogs::studies());
        echo $_;
    }
    
    public function testapi(){
        $API      = API::initialize("http://69.94.133.56/khorPilares");
        $data     = array(
            "userName"   =>"5638563485",
            "LastName"   =>"Roman",
            "FirstName"  =>"Sergio",
            "AccessCode" =>"hrwek23skjhdf",
            );
        $response = $API->exec("srvPilares.asp",$data);
        echo $response;
    }
    
}

?>
