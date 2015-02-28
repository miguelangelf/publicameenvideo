<?php

class home extends _controller {

    public function index() {
        $Categories = $this->Module("sidemenu")->init("CategoriesMenu","categories","left","slide");
        $data = array(
          "title" => "Publicameenvideo",
            "CategoriesMenu" => $Categories->html()
        );
        $this->view("home", $data);
    }
}
