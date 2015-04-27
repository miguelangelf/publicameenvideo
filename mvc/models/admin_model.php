<?php

class admin_model {

    public function select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxnumber, $order, $extracondition, $filterfield, $condition, $filterarg) {


        $qfields = implode(",", $fieldstoselect);
        $qwhere = "";
        for ($i = 0; $i < sizeof($fieldstosearch); $i++) {
            $qwhere.=$fieldstosearch[$i];
            $qwhere.=" like '%";
            $qwhere.=$search;
            $qwhere.="%'";

            if ($i < sizeof($fieldstosearch) - 1) {
                $qwhere.=" OR ";
            }
        }

        $extra = "";
        if ($extracondition != NULL) {

            $extra = "AND (" . $extracondition . " )";
        }


        $filter = "";
        if ($filterfield != NULL) {

            $filter.=" AND " . $filterfield . " " . $condition . " " . $filterarg . "";
        }

        $orderby = "";

        if ($order != NULL) {
            $orderby = "ORDER BY " . $order;
        }



        $query = "SELECT DISTINCT " . $qfields . " FROM " . $table . " WHERE (" . $qwhere . ")" . $extra . " " . $filter . " " . $orderby . " LIMIT " . ($page * $maxnumber) . " , " . $maxnumber;


        $pdo = Database::executeConn($query, "publicameenvideo");
        $results = array();
        while ($row = Database::fetch_array($pdo)) {

            $results[] = $row;
        }

        // return array("query" => $query);
        return $results;
    }

    public function insert($table, $val) {
        $values = implode(",", $val);
        $query = "INSERT INTO " . $table . " VALUES (" . $values . ") ";
        $lacon = Database::getExternalConnection("publicameenvideo");
        $elquery = $lacon->query($query);
        $lastid = $lacon->lastInsertId();
        return $lastid;
    }

    public function delete($table, $idfield, $lista) {
        $strlist = implode(",", $lista);
        $sql = "Delete from " . $table . " where " . $idfield . " in(" . $strlist . ")";
        $pdo = Database::executeConn($sql, "publicameenvideo");
        return true;
    }

    public function countusers($actual, $message) {
        $aux = $message;
        $sql = "SELECT COUNT(*) FROM us_users where name like '%" . $aux . "%' or email like '%" . $aux . "%' or last_name like '%" . $aux . "%'   LIMIT " . ($actual * 20) . " ,20";
        $pdo = Database::executeConn($sql, "default");
        $results = array();
        $row = Database::fetch_array($pdo);
        $results[] = $row;

        return $results;
    }

    public function disable_users() {
        $sql = "SELECT COUNT(id) as no FROM users WHERE token = 0";
        $pdo = Database::executeConn($sql, "publicameenvideo");
        $row = Database::fetch_row($pdo);
        return $row["no"];
    }

    public function unaprov() {
        $sql = "SELECT COUNT(id) as no FROM videos WHERE status_id = 1";
        $pdo = Database::executeConn($sql, "publicameenvideo");
        $row = Database::fetch_row($pdo);
        return $row["no"];
    }

    public function numofcompanies() {
        $sql = "SELECT COUNT(id) as no FROM companies";
        $pdo = Database::executeConn($sql, "publicameenvideo");
        $row = Database::fetch_row($pdo);
        return $row["no"];
    }

    public function empresas() {
        $sql = "SELECT name,email FROM us_users LIMIT 20";
        $pdo = Database::executeConn($sql, "default");
        $results = array();
        while ($row = Database::fetch_array($pdo)) {
            $results[] = $row;
        }
        return $results;
    }

    public function videos() {
        $sql = "SELECT email FROM us_users LIMIT 20";
        $pdo = Database::executeConn($sql, "default");
        $results = array();
        while ($row = Database::fetch_array($pdo)) {
            $results[] = $row;
        }
        return $results;
    }

}

?>
