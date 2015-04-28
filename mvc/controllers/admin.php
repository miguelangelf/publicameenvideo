<?php

class admin extends _controller {

    public function dashboard() {
        $this->view("dashboard", null);
    }
    
    public function getmoreofuser()
    {
        
        
        $theid = $this->Post("id");
        
             


        $table = "users,roles,plans,status";
        $fieldstoselect = array("users.id","birthdate","users.gender as sexo", "users.name as nombre", "users.last_name as apellido", "users.email", "users.plan_expiration as expiration","users.created as creado","roles.name as rolename","plans.name as planname","status.name as statusname");
        $fieldstosearch = array("users.id");
        $search = "";
        $page = 0;
        $maxitems = 20;
        $order = NULL;
        $tablerelation = "role_id=roles.id AND plan_id=plans.id AND status_id=status.id AND users.id=$theid";
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;

        $moreuser = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $data["infouser"] = $moreuser;
        $data["infochannel"] = $moreuser;

        $this->view("UserData", $data);
        
        
    }

    public function insertusarios() {
        $id = "NULL";
        $gender = $this->Post("gender");
        $birthdate = $this->Post("birthdate");
        $name = "'" . $this->Post("name") . "'";
        $lastname = "'" . $this->Post("lastname") . "'";
        $email = "'" . $this->Post("email") . "'";
        $role = $this->Post("role");
        $created = "NOW()";
        $lastaccess = "NOW()";
        $plan = $this->Post("plan");
        $expiration = $this->Post("expiration");
        $status = $this->Post("status");
        $token = "''";
        $table = "users";

        $birthdate = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $birthdate)));
        $expiration = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $expiration)));
        $birthdate = "'" . $birthdate . "'";
        $expiration = "'" . $expiration . "'";


        $datos = array($id, $gender, $birthdate, $name, $lastname, $email, $role, $created, $lastaccess, $token, $plan, $expiration, $status);
        $thelastid = $this->Model()->insert($table, $datos);


        $table = "users_paswd";
        $password = "'" . $this->Post("password") . "'";
        $newdatos = array($thelastid, $password);
        $resp = $this->Model()->insert($table, $newdatos);

        
        $photo = $this->Post("photo");
        $path = Config::get("Core.Path.theme") . "data/tmp/";
        $fichero = $path . $photo;        
        $filesize=filesize($fichero);
        $name=$photo;
        $created=date ("Y-m-d H:i:s", filectime($fichero));
        $extension= pathinfo($fichero, PATHINFO_EXTENSION);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime=finfo_file($finfo,$fichero);
        finfo_close($finfo);
        $table="files";
        
        
        $datosfile=array("NULL","'$photo'","''","'$created'",$filesize,"'$extension'","'$mime'",$thelastid);
       $resp = $this->Model()->insert($table, $datosfile);

        
       // echo($resp);
    }

    public function insertempresas() {
        
    }

    public function insertvideos() {
        
    }

    //SELECCIONA USUARIOS EMPRESAS VIDEOS

    public function usuarios() {

        $nameofcat = "";

        $name = $this->Post("pagename");
        $page = $this->Post("actualpage");
        $category = $this->Post("category");
        $search = $this->Post("search");

        $nameofcat = "";

        $table = "users,roles";
        $fieldstoselect = array("users.id", "users.name", "users.last_name", "users.email", "roles.name as rolename");
        $fieldstosearch = array("users.id", "users.name", "users.last_name", "users.email");
        $search = $search;
        $page = $page;
        $maxitems = 20;
        $order = NULL;
        $tablerelation = "users.role_id=roles.id";
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;


        switch ($category) {
            case 0:
                $nameofcat = "Sin Filtro";
                $filterfield = NULL;
                $condition = NULL;
                $filterarg = NULL;
                break;
            case 1:
                $nameofcat = "Expirados";
                $filterfield = "plan_expiration";
                $condition = "<";
                $filterarg = "NOW()";
                break;
            case 2:
                $nameofcat = "Proximos a vencer";
                $filterfield = "plan_expiration";
                $condition = " >= NOW() AND plan_expiration <=";
                $filterarg = "NOW() + INTERVAL 1 MONTH";
                break;
            case 3:
                $nameofcat = "Recientemente Conectados";
                $filterfield = "plan_expiration";
                $condition = "<";
                $filterarg = "NOW()";
                break;
        }

        $users = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $data["inbox"] = $name . " :: " . $nameofcat;
        $data["path"] = Config::get("Theme.Web.uploads");
        $data["users"] = $users;

        $this->view("usuarios", $data);
    }

    public function empresas() {



        $nameofcat = "";

        $name = $this->Post("pagename");
        $page = $this->Post("actualpage");
        $category = $this->Post("category");
        $search = $this->Post("search");

        $nameofcat = "";

        $table = "companies";
        $fieldstoselect = array("companies.id", "companies.name", "companies.phone", "companies.email", "companies.description");
        $fieldstosearch = $fieldstoselect;
        $search = $search;
        $page = $page;
        $maxitems = 20;
        $order = NULL;
        $tablerelation = NULL;
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;


        $nameofcat = "";
        switch ($category) {
            case 0:
                $nameofcat = "Sin Filtro";
                $filterfield = NULL;
                $condition = NULL;
                $filterarg = NULL;


                break;
            case 1:
                $nameofcat = "Registrados Recientemente";
                $filterfield = "created";
                $condition = " <= NOW() AND created >=";
                $filterarg = "NOW() - INTERVAL 1 MONTH";
        }

        $empresas = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $data["inbox"] = $name . " :: " . $nameofcat;
        $data["path"] = Config::get("Theme.Web.uploads");
        $data["empresas"] = $empresas;
        $this->view("empresas", $data);
    }

    public function videos() {

        $nameofcat = "";

        $name = $this->Post("pagename");
        $page = $this->Post("actualpage");
        $category = $this->Post("category");
        $search = $this->Post("search");

        $nameofcat = "";

        $table = "videos,companies,status,categories";
        $fieldstoselect = array("videos.id", "videos.title", "videos.category_id", "categories.name as categoria", "videos.company_id", "videos.description", "companies.name as company", "videos.status_id", "status.name as status", "videos.keywords_search");
        $fieldstosearch = array("videos.id", "categories.name", "videos.title", "videos.description", "videos.company_id", "videos.status_id", "companies.name");
        $search = $search;
        $page = $page;
        $maxitems = 20;
        $order = NULL;
        $tablerelation = "videos.company_id=companies.id AND status.id=videos.status_id AND category_id=categories.id";
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;




        $nameofcat = "";
        switch ($category) {
            case 0:
                $nameofcat = "Sin Filtro";
                $filterfield = NULL;
                $condition = NULL;
                $filterarg = NULL;


                break;
            case 1:
                $nameofcat = "En Espera";
                $filterfield = "videos.status_id";
                $condition = "=";
                $filterarg = "1";


                break;
            case 2:
                $nameofcat = "Activos";
                $filterfield = "videos.status_id";
                $condition = " =";
                $filterarg = "2";


                break;
            case 3:
                $nameofcat = "Inactivos";
                $filterfield = "videos.status_id";
                $condition = "=";
                $filterarg = "3";


                break;

            case 4:
                $nameofcat = "Inactivos";
                $filterfield = "Bloqueados";
                $condition = "=";
                $filterarg = "3";


                break;
        }




        $videos = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);


        $data["inbox"] = $name . " :: " . $nameofcat;
        //   $data["inbox"]=$users;
        $data["path"] = Config::get("Theme.Web.uploads");
        $data["videos"] = $videos;
        //  $data["max"] = $users;
        $this->view("videos", $data);
    }

    //BLOQUEA,ELIMINA

    public function delusuarios() {

        $elementsjson = $this->Post("elements");
        $elements = json_decode($elementsjson);
        $action = $this->Post("action");
        if ($action == 1)
            $this->Model()->delete("users", "id", $elements);
    }

    public function delempresas() {

        $elementsjson = $this->Post("elements");
        $elements = json_decode($elementsjson);
        $action = $this->Post("action");
        if ($action == 1)
            $this->Model()->delete("companies", "id", $elements);
    }

    public function delvideos() {

        $elementsjson = $this->Post("elements");
        $elements = json_decode($elementsjson);
        $action = $this->Post("action");
        if ($action == 1)
            $this->Model()->delete("videos", "id", $elements);
    }

    //OBTIENE CANTIDAD DE VIDEOS; USERS; EMPRESAS

    public function notificaciones() {
        $type = $this->Get(1);
        $disable_users = $this->Model()->disable_users();
        $unapprovedvideos = $this->Model()->unaprov();
        $numofcompanies = $this->Model()->numofcompanies();




        echo json_encode(array("no_users" => $disable_users, "no_unapproved" => $unapprovedvideos, "no_companies" => $numofcompanies));
    }

    //SUBIR ARCHIVO

    public function subirarchivo() {
        $path = Config::get("Core.Path.theme") . 'includes/UploadHandler.php';
        require($path);
        $upload_handler = new UploadHandler();
    }

    public function getitemstoinsertuser() {

        $fieldstoselect = array("*");
        $fieldstosearch = array("id");
        $search = "";
        $page = 0;
        $maxitems = 20;
        $order = NULL;
        $tablerelation = NULL;
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;

        $role = $this->Model()->select("roles", $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);
        $plan = $this->Model()->select("plans", $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);
        $status = $this->Model()->select("status", $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);



        $data["role"] = $role;
        $data["plan"] = $plan;
        $data["status"] = $status;

        $this->view("FormInsertUser", $data);


    }
    
    
    public function getitemstoinsertcompany()
    {
        $this->view("FormInsertCompany", $data);
        
    }

}

?>