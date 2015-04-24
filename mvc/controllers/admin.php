<?php

class admin extends _controller {

    public function dashboard() {
        $this->view("dashboard", null);
    }

    public function insertuser() {
        $birthdate = $this->Post("birthdate");
        $email = $this->Post("email");
        $gender = $this->Post("gender");
        $lastname = $this->Post("lastname");
        $name = $this->Post("name");
        $plan = $this->Post("plan");
        $role = $this->Post("role");
        $token = $this->Post("token");
        $this->Model()->insertuser($birthdate, $email, $gender, $lastname, $name, $plan, $role, $token);
    }

    public function itsFile() {

        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
    }

    public function usuarios() {
        //$table, $fieldstoselect, $fieldstosearch, $search, $page, $maxnumber,$order,$filterfield,$filterarg
        //RECIBE
        $name = $this->Post("pagename");
        $page = $this->Post("actualpage");
        $category = $this->Post("category");
        $search = $this->Post("search");



        //Prepara los datos

        $fieldstoselect = array("id", "name", "last_name", "email", "plan_expiration");
        $tabla = "users";
        $maxitems = 20;




        $filterfield;
        $condition;
        $filterarg;

        $nameofcat = "";
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



        //$table, $fieldstoselect, $fieldstosearch, $search, $page, $maxnumber, $order, $filterfield,$condition, $filterarg)
        $users = $this->Model()->select($tabla, $fieldstoselect, $fieldstoselect, $search, $page, $maxitems, NULL, $filterfield, $condition, $filterarg);

        //REGRESA 

        $data["inbox"] = $name . " :: " . $nameofcat;
        //   $data["inbox"]=$users;
        $data["path"] = Config::get("Theme.Web.uploads");
        $data["users"] = $users;
      //  $data["max"] = $users;
        $this->view("usuarios", $data);
    }

    public function checkusuarios() {

        $elementsjson = $this->Post("elements");
        $elements = json_decode($elementsjson);
        $action = $this->Post("action");
        if ($action == 1)
            $this->Model()->delete("users", "id", $elements);
    }

    public function empresas() {
        $inbox_number = $this->Post("inbox");
        $data["inbox"] = $inbox_number;

        $users = $this->Model()->empresas();
        $data["users"] = $users;


        $this->view("empresas", $data);
    }

    public function videos() {
        //$table, $fieldstoselect, $fieldstosearch, $search, $page, $maxnumber,$order,$filterfield,$filterarg
        //RECIBE
        $name = $this->Post("pagename");
        $page = $this->Post("actualpage");
        $category = $this->Post("category");
        $search = $this->Post("search");



        //Prepara los datos

        $fieldstoselect = array("videos.id", "title","category_id as categoria", "description","company_id as company", "user_id as user", "status_id as status","keywords_search");
        $fieldstosearch = array("videos.id", "title", "description","company_id", "user_id", "status_id");
        $tabla = "videos,users";
        $maxitems = 20;




        $filterfield;
        $condition;
        $filterarg;

        $nameofcat = "";
        switch ($category) {
            case 0:
                $nameofcat = "Sin Filtro";
                $filterfield = NULL;
                $condition = NULL;
                $filterarg = NULL;


                break;
            case 1:
                $nameofcat = "Esperando Confirmación";
                $filterfield = "status_id";
                $condition = "=";
                $filterarg = "0";


                break;
            case 2:
                $nameofcat = "Aprobados";
                $filterfield = "status_id";
                $condition = " =";
                $filterarg = "1";


                break;
            case 3:
                $nameofcat = "Suspendidos";
                $filterfield = "status_id";
                $condition = "=";
                $filterarg = "2";


                break;
        }



        //$table, $fieldstoselect, $fieldstosearch, $search, $page, $maxnumber, $order, $filterfield,$condition, $filterarg)
        $videos = $this->Model()->select($tabla, $fieldstoselect, $fieldstosearch, $search, $page, $maxitems, NULL, $filterfield, $condition, $filterarg);

        //REGRESA 

        $data["inbox"] = $name . " :: " . $nameofcat;
        //   $data["inbox"]=$users;
        $data["path"] = Config::get("Theme.Web.uploads");
        $data["videos"] = $videos;
      //  $data["max"] = $users;
        $this->view("videos", $data);
    }

    
    
    
    public function notificaciones() {
        $type = $this->Get(1);
        $disable_users = $this->Model()->disable_users();
        $unapprovedvideos = $this->Model()->unaprov();
        $numofcompanies = $this->Model()->numofcompanies();




        echo json_encode(array("no_users" => $disable_users, "no_unapproved" => $unapprovedvideos, "no_companies" => $numofcompanies));
    }

}

?>