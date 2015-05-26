<?php

class admin_model {

    public function select($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxnumber, $order, $extracondition, $filterfield, $condition, $filterarg) {


        $qfields = implode(",", $fieldstoselect);

        $qwhere = "";
        if ($fieldstosearch != NULL) {
            $qwhere.="(";
            for ($i = 0; $i < sizeof($fieldstosearch); $i++) {
                $qwhere.=$fieldstosearch[$i];
                $qwhere.=" like '%";
                $qwhere.=$search;
                $qwhere.="%'";

                if ($i < sizeof($fieldstosearch) - 1) {
                    $qwhere.=" OR ";
                }
            }
            $qwhere.=")";
        }

        $extra = "";
        if ($extracondition != NULL) {

            if ($fieldstosearch != NULL)
                $extra.=" AND ";
            $extra.= " (" . $extracondition . " )";
        }


        $filter = "";
        if ($filterfield != NULL) {

            $filter.=" AND " . $filterfield . " " . $condition . " " . $filterarg . "";
        }

        $orderby = "";
        if ($order != NULL) {
            $orderby = "ORDER BY " . $order;
        }



        $query = "SELECT DISTINCT " . $qfields . " FROM " . $table . " WHERE " . $qwhere . "" . $extra . " " . $filter . " " . $orderby . " LIMIT " . ($page * $maxnumber) . " , " . $maxnumber;


        $pdo = Database::executeConn($query, "publicameenvideo");
        $results = array();
        while ($row = Database::fetch_array($pdo)) {

            $results[] = $row;
        }

        return $results;
    }

    public function showselect($table, $fieldstoselect, $fieldstosearch, $search, $page, $maxnumber, $order, $extracondition, $filterfield, $condition, $filterarg) {




       $qfields = implode(",", $fieldstoselect);

        $qwhere = "";
        if ($fieldstosearch != NULL) {
            $qwhere.="(";
            for ($i = 0; $i < sizeof($fieldstosearch); $i++) {
                $qwhere.=$fieldstosearch[$i];
                $qwhere.=" like '%";
                $qwhere.=$search;
                $qwhere.="%'";

                if ($i < sizeof($fieldstosearch) - 1) {
                    $qwhere.=" OR ";
                }
            }
            $qwhere.=")";
        }

        $extra = "";
        if ($extracondition != NULL) {

            if ($fieldstosearch != NULL)
                $extra.=" AND ";
            $extra.= " (" . $extracondition . " )";
        }


        $filter = "";
        if ($filterfield != NULL) {

            $filter.=" AND " . $filterfield . " " . $condition . " " . $filterarg . "";
        }

        $orderby = "";
        if ($order != NULL) {
            $orderby = "ORDER BY " . $order;
        }



        $query = "SELECT DISTINCT " . $qfields . " FROM " . $table . " WHERE " . $qwhere . "" . $extra . " " . $filter . " " . $orderby . " LIMIT " . ($page * $maxnumber) . " , " . $maxnumber;


      
        return $query;
    }

    public function insert($table, $val) {
        $values = implode(",", $val);
        $query = "INSERT INTO " . $table . " VALUES (" . $values . ") ";
        $lacon = Database::getExternalConnection("publicameenvideo");
        $elquery = $lacon->query($query);
        $lastid = $lacon->lastInsertId();
        return $lastid;
    }

    public function showinsert($table, $val) {
        $values = implode(",", $val);
        $query = "INSERT INTO " . $table . " VALUES (" . $values . ") ";

        return $query;
    }

    public function delete($table, $idfield, $lista) {
        $strlist = implode(",", $lista);
        $sql = "Delete from " . $table . " where " . $idfield . " in(" . $strlist . ")";
        $pdo = Database::executeConn($sql, "publicameenvideo");
        return true;
    }

    public function showdelete($table, $idfield, $lista) {
        $strlist = implode(",", $lista);
        $sql = "Delete from " . $table . " where " . $idfield . " in(" . $strlist . ")";
        return $sql;
    }

    public function count($table, $where) {
        $query = "SELECT COUNT(*) as cnt FROM $table where $where";
        //return $query;
        $pdo = Database::executeConn($query, "publicameenvideo");
        
        $row = Database::fetch_row($pdo);
        return $row["cnt"];
        
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

}

?>
