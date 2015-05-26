<?php

class admin extends _controller {

    public function dashboard() {
        $this->view("dashboard", null);
    }

    public function getmoreofuser() {

        /*
         * A PARTIR DEL ID ($theid) SE BUSCA TODA LA INFORMACION
         * DEL USUARIO, AL FINAL SE OBTIENE SU FOTO
         * 
         */
        $theid = $this->Post("id");

        $table = "users,roles,plans,status";
        $fieldstoselect = array("users.id", "birthdate", "users.gender as sexo", "users.name as nombre", "users.last_name as apellido", "users.email", "users.plan_expiration as expiration", "users.created as creado", "roles.name as rolename", "plans.name as planname", "status.name as statusname");
        $fieldstosearch = NULL;
        $search = NULL;
        $page = 0;
        $maxitems = 1;
        $order = NULL;
        $tablerelation = "role_id=roles.id AND plan_id=plans.id AND status_id=status.id AND users.id=$theid";
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;

        $moreuser = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $table = "users,files";
        $fieldstoselect = array("files.name as nombre");
        $fieldstosearch = array("files.id");
        $tablerelation = "users.id=$theid AND users.file_id=files.id";

        $photo = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $data["infouser"] = $moreuser;
        $data["photo"] = $photo;

        $this->view("UserData", $data);
    }

    public function getmoreofvideo() {
        $theid = $this->Post("id");

        $table = "videos,files,companies,status,categories";
        $fieldstoselect = array("videos.id as id", "videos.title as titulo", "videos.description as descripcion", "videos.likes as likes", "videos.views as vistas", "companies.name as companyname", "status.name as statusname", "categories.name as categoryname", "videos.location_id as locid", "videos.latitude as latitud", "videos.longitude as longitud", "videos.last_view as ultimavisita", "videos.created as creado", "videos.ranking as ranking", "videos.friendly_url as furl");
        $fieldstosearch = NULL;
        $search = NULL;
        $page = 0;
        $maxitems = 1;
        $order = NULL;
        $tablerelation = " videos.company_id=companies.id AND status_id=status.id AND category_id=categories.id AND videos.id=$theid";
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;

        $morevideo = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $data["infovideo"] = $morevideo;
        $data["infochannel"] = $morevideo;


        $table = "files,videos";
        $fieldstoselect = array("name as nombre");
        $fieldstosearch = array("files.id");
        $tablerelation = "videos.id=$theid AND videos.file_id=files.id";

        $video = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $data["video"] = $video[0][0];



        $this->view("VideoData", $data);
    }

    public function getmoreofcompany() {


        $theid = $this->Post("id");


        $table = "users,roles,plans,status,companies";
        $fieldstoselect = array("companies.id as id", "companies.name as nombre", "companies.RFC as rfc", "companies.address as direccion", "companies.description as descripcion", "companies.phone as telefono", "companies.email as email", "companies.location_id as ubicacion", "companies.latitude as latitud", "companies.longitude as longitud", "companies.created as fechacreacion");
        $fieldstosearch = NULL;
        $search = NULL;
        $page = 0;
        $maxitems = 1;
        $order = NULL;
        $tablerelation = "role_id=roles.id AND plan_id=plans.id AND status_id=status.id AND companies.id=$theid";
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;

        $morecompany = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $table = "companies,files";
        $fieldstoselect = array("files.name as nombre");
        $fieldstosearch = array("files.id");
        $tablerelation = "companies.id=$theid AND companies.file_id=files.id";

        $photo = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        $data["photo"] = $photo;
        $data["infochannel"] = $moreuser;


        $data["infocompany"] = $morecompany;

        $this->view("CompanyData", $data);
    }

    public function storedemail($table, $where) {
        $numofemails = $this->Model()->count($table, $where);
        $intnum = intval($numofemails);
        //echo $intnum;

        if ($intnum != 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function insertusarios() {

        $errores = array();

        $email = $this->Post("email");
        $table = "users";
        $where = "email='$email'";

        $res = $this->storedemail($table, $where);

        if ($res == 1) {
            $errores[sizeof($errores)] = "E1";
        }


        //INSERTAR LA FOTO EN LA 
        $photo = $this->Post("photo");
        $id = "NULL";
        $path = Config::get("Core.Path.theme") . "data/tmp/";
        $fichero = $path . $photo;
        $filesize = filesize($fichero);
        $name = $photo;
        //   $created = date("Y-m-d H:i:s", filectime($fichero));
        $created = "NOW()";
        $extension = pathinfo($fichero, PATHINFO_EXTENSION);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fichero);
        if (strpos($mime, 'image') === false) {
            $errores[sizeof($errores)] = "F1";
        }

        if (sizeof($errores) != 0) {
            echo (json_encode($errores));
            return;
        }


        //A partir de aqui no hay errores y se almcaena en la base de datos

        finfo_close($finfo);
        $table = "files";
        $datosfile = array("NULL", "'$photo'", "''", "'$created'", $filesize, "'$extension'", "'$mime'");
        $idofphoto = $this->Model()->insert($table, $datosfile);


        //INSERTAR USUARIO 
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


        //Insertar contraseña

        $datos = array($id, $gender, $birthdate, $name, $lastname, $email, $role, $created, $lastaccess, $token, $plan, $expiration, $status, $idofphoto);
        $thelastid = $this->Model()->insert($table, $datos);


        $table = "users_paswd";
        $password = "'" . $this->Post("password") . "'";
        $newdatos = array($thelastid, $password);
        $resp = $this->Model()->insert($table, $newdatos);



        $errores[sizeof($errores)] = "OK";

        echo (json_encode($errores));
    }

    public function insertempresas() {

        $errores = array();

        $email = $this->Post("email");
        $table = "companies";
        $where = "email='$email'";


        $res = $this->storedemail($table, $where);

        if ($res == 1) {

            $errores[sizeof($errores)] = "E1";
        }


        $photo = $this->Post("photo");
        $path = Config::get("Core.Path.theme") . "data/tmp/";
        $fichero = $path . $photo;
        $filesize = filesize($fichero);
        $name = $photo;
        $created = "NOW()";
        $extension = pathinfo($fichero, PATHINFO_EXTENSION);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $fichero);
        finfo_close($finfo);

        if (strpos($mime, 'image') === false) {
            $errores[sizeof($errores)] = "F1";
        }

        if (sizeof($errores) != 0) {
            echo (json_encode($errores));
            return;
        }



        $table = "files";

        $datosfile = array("NULL", "'$photo'", "''", "'$created'", $filesize, "'$extension'", "'$mime'");
        $idofphoto = $this->Model()->insert($table, $datosfile);



        $id = "NULL";
        $name = "'" . $this->Post("name") . "'";
        $rfc = "'" . $this->Post("rfc") . "'";
        $address = "'" . $this->Post("address") . "'";
        $description = "'" . $this->Post("descripcion") . "'";
        $phone = "'" . $this->Post("phone") . "'";
        $email = "'" . $this->Post("email") . "'";
        $location_id = 0;
        $latitude = 0;
        $longitude = 0;
        $created = "NOW()";
        $table = "companies";



        $datos = array($id, $name, $rfc, $address, $description, $phone, $email, $location_id, $latitude, $longitude, $created, $idofphoto);
        $thelastid = $this->Model()->insert($table, $datos);


        $table = "company_passwd";
        $password = "'" . $this->Post("password") . "'";
        $newdatos = array($thelastid, $password);
        $resp = $this->Model()->insert($table, $newdatos);

        echo("OK");
    }

    public function insertfile($filename) {

        $directory = Config::get("Core.Path.theme") . "data/tmp/";
        $path = $directory . $filename;

        $id = "NULL";
        $name = "'$filename'";
        $location = "''";
        $created = "NOW()";
        $size = filesize($path);
        $extension = "'" . pathinfo($path, PATHINFO_EXTENSION) . "'";
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = "'" . finfo_file($finfo, $path) . "'";
        finfo_close($finfo);

        $fileinfo = array($id, $name, $location, $created, $size, $extension, $mime);

        return $fileinfo;
    }

    public function checkmime($filename, $expected) {
        $directory = Config::get("Core.Path.theme") . "data/tmp/";
        $path = $directory . $filename;
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        if (strpos($mime, $expected) === false)
            return false;
        else
            return true;
    }

    public function insertvideo() {

        /*
         * 
         * ACTUALMENTE ESTE METODO RECIBE LA INFORMACION DE UN VIDEO
         * GUARDA EL ARCHIVO DE VIDEO EN LA TABLA "FILES"
         * ESPERA EL ID QUE SE ASIGNO AL ARCHIVO Y SE GUARDA EN LA TABLA "VIDEOS"
         */

        $errores = array();

        //campos para la tabla videos
        $id = "NULL";
        $title = $this->Post("title");
        $description = $this->Post("description");
        $likes = 0;
        $file_id;
        $views = 0;
        $company_id = $this->Post("company_id");
        $status_id = $this->Post("company_id");
        $category_id = $this->Post("category_id");
        $location_id = 0;
        $latitude = 0;
        $longitude = 0;
        $lastview = "NOW()";
        $created = "NOW()";
        $ranking = 0;
        $keywords_search="";
        $friendly_url="";




        //campos para la tabla files
        $filename = $this->Post("file_name");
        $fileinfo = $this->insertfile($filename);
        $table = "files";

        $validmime = "video/webm";
        $isvalidfile = $this->checkmime($filename, $validmime);

        if (!$isvalidfile)
            $errores[sizeof($errores)] = "F1";

        if (sizeof($errores) > 0) {
            echo(json_encode($errores));
            return;
        }

        $file_id = $this->Model()->insert($table, $fileinfo);


        $table = "videos";
        $datos = array($id, "'$title'", "'$description'", $likes, $file_id, $views, $company_id, $status_id, $category_id, $location_id, $latitude, $longitude, $lastview, $created, $ranking, "'$keywords_search'","'$friendly_url'");
        $thelastid = $this->Model()->insert($table, $datos);
        
       
        $errores[sizeof($errores)]="OK";
        echo(json_encode($errores));
    }

    //SELECCIONA USUARIOS EMPRESAS VIDEOS

    public function usuarios() {

        //sleep(3);
        $nameofcat = "";

        $name = $this->Post("pagename");
        $page = $this->Post("actualpage");
        $category = $this->Post("category");
        $search = $this->Post("search");

        $nameofcat = "";

        $table = "users,roles,status";
        $fieldstoselect = array("users.id", "users.name", "users.last_name", "users.email", "roles.name as rolename", "status.name as statusname");
        $fieldstosearch = array("users.id", "users.name", "users.last_name", "users.email");
        $search = $search;
        $page = $page;
        $maxitems = 20;
        $order = NULL;
        $tablerelation = "users.role_id=roles.id AND status.id=users.status_id";
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

        //$fieldstosearch = NULL;
        //$role2 = $this->Model()->showselect("roles", $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);



        $data["role"] = $role;
        $data["plan"] = $plan;
        $data["status"] = $status;
        $data["auxiliarinfo"] = $role2;

        $this->view("FormInsertUser", $data);
    }

    public function getitemstoinsertcompany() {
        $this->view("FormInsertCompany", $data);
    }

    public function getitemstoinsertvideo() {


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

        $categories = $this->Model()->select("categories", $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);


        $data["categories"] = $categories;
        $this->view("FormInsertVideo", $data);
    }

    public function availablecompanies() {

        $name = $this->Post("name");
        $table = "companies";
        $fieldstoselect = array("companies.name as label", "companies.id as id", "companies.email as email");
        $fieldstosearch = array("companies.name");
        $search = $name;
        $page = 0;
        $maxitems = 20;
        $order = NULL;
        $tablerelation = NULL;
        $filterfield = NULL;
        $condition = NULL;
        $filterarg = NULL;

        $moreuser = $this->Model()->select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, $order, $tablerelation, $filterfield, $condition, $filterarg);

        echo json_encode($moreuser);
    }

}

?>