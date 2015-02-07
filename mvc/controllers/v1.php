<?php
class v1 extends _controller{
    
    private $parameters = array(
        // name, type, max-size, min, max
        array("userName","string",20,null,null),
        array("Perfil1","float",3,0.0,100.0),
        array("Perfil2","float",3,0.0,100.0),
        array("Perfil3","float",3,0.0,100.0),
        array("Perfil4","float",3,0.0,100.0),
        array("Factor1","int",2,0,10),
        array("Factor2","int",2,0,10),
        array("Factor3","int",2,0,10),
        array("Factor4","int",2,0,10),
        array("Factor5","int",2,0,10),
        array("Factor6","int",2,0,10),
        array("Factor7","int",2,0,10),
        array("Factor8","int",2,0,10),
        array("Factor9","int",2,0,10),
        array("Factor10","int",2,0,10),
        array("Factor11","int",2,0,10),
        array("Factor12","int",2,0,10),
        array("Factor13","int",2,0,10),
        array("Factor14","int",2,0,10),
        array("Factor15","int",2,0,10),
        array("Factor16","int",2,0,10)
    );
    
    public function evaluation(){
        if(POST::isReady()){
            $this->validateAll();
            $this->addEvaluation();
            $this->response(TRUE, "0");                
        }else{
            $this->response(FALSE,"Post http request only",1);
        }
    }
    
    public function update(){
        if(POST::isReady()){
            $this->validateAll();
            $this->updateEvaluation();
            $this->response(TRUE, "0");                
        }else{
            $this->response(FALSE,"Post http request only",1);
        }
    }
    
    private function folioExists($folio){
        $sql = "SELECT * FROM candidate_evaluations WHERE folio='$folio'";
        $pdo = Database::executeConn($sql,"pilares");
        $num = Database::num_rows($pdo);
        if($num>0){
            return true;
        }
        return false;
    }
    
    private function addEvaluation(){
        $folio = $this->Post($this->parameters[0][0]);
        if($this->folioExists($folio)){
            $this->response(FALSE,"Parameter userName already exists.",4);
        }
        $sql = "INSERT INTO candidate_evaluations VALUES (";
        $first = true;
        foreach($this->parameters as $parameter){
            if($first){
                $sql.= "'".$this->Post($parameter[0])."'";
                $first = false;
            }else{
                $sql.= ",'".$this->Post($parameter[0])."'";
            }
        }
        $sql.= ")";        
        Database::executeConn($sql,"pilares");
        $_update = "UPDATE candidates SET exam_finished = 1 WHERE folio = '$folio'";
        Database::executeConn($_update,"pilares");
    }
    
    private function updateEvaluation(){        
        $folio = $this->Post($this->parameters[0][0]);
        if(!$this->folioExists($folio)){
            $this->addEvaluation();
        }else{                        
            $sql = "UPDATE candidate_evaluations SET ";
            $first = true;
            foreach($this->parameters as $parameter){
                if($parameter[0] != "userName"){
                    if($first){
                        $sql.= $parameter[0]."='".$this->Post($parameter[0])."'";
                        $first = false;
                    }else{
                        $sql.= ",".$parameter[0]."='".$this->Post($parameter[0])."'";
                    }
                }
            }
            $sql .= " WHERE folio = '$folio'";            
            Database::executeConn($sql,"pilares");            
            $_update = "UPDATE candidates SET exam_finished = 1 WHERE folio = '$folio'";
            Database::executeConn($_update,"pilares");
        }        
    }
    
    private function response($response,$message,$code = null){
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: POST");
        header('Content-Type: application/json');
        if($response){
            http_response_code(202);  // Accepted
            echo json_encode(array("response"=>$message));
        }else{
            http_response_code(400);  // Bad request
            echo json_encode(array("errors"=>$message,"code"=>$code));
        }
        exit;
    }
    
    private function validateAll(){
        foreach($this->parameters as $parameter){
            $this->validate($parameter);
        }
    }
    
    private function validate($parameter){
        $name = $parameter[0];
        $type = $parameter[1];
        $size = $parameter[2];
        $min  = $parameter[3];
        $max  = $parameter[4];
        if(!POST::exists($name)){
            $this->response(FALSE,"Parameter ".$name." is required.",3);
        }
        $value = $this->Post($name);
        if(is_null($value)){
            $this->response(FALSE,"Parameter ".$name." is invalid.",2);
        }
        if($value == ""){
            $this->response(FALSE,"Parameter ".$name." is invalid.",2);
        }
        switch($type){
            case "string":
                if(strlen($value)>$size){
                    $this->response(FALSE,"Parameter ".$name." is invalid.",2);
                }
                break;
            case "float":
                $value = round($value, 2);
                if(!is_float($value)){
                    $this->response(FALSE,"Parameter ".$name." is invalid.",2);
                }
                if($value<$min || $value>$max){
                    $this->response(FALSE,"Parameter ".$name." is invalid.",2);
                }
                break;
            case "int":
                if(!is_int((int)$value)){
                    var_dump($value);
                    $this->response(FALSE,"Parameter ".$name." is invalid - Not INTEGER value.",2);
                }
                if($value<$min || $value>$max){
                    $this->response(FALSE,"Parameter ".$name." is invalid - Out of valid range",2);
                }
                break;
        }
    }
}
