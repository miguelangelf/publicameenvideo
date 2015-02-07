<?php

class jobmarkers_model{
	
    public function getMarkers(){
        $sql = "SELECT id,position as title, salary,company,lat,lng as lon, hash, source, id_source FROM bash_stable_jobs GROUP BY lat,lng ORDER BY rate DESC limit 10000";
        $pdo = Database::executeConn($sql,"pilares");
        $resp = array();
        while($r = Database::fetch_assoc($pdo)){
	    $r["title"] = utf8_encode($r["title"]);
	    $r["company"] = utf8_encode($r["company"]);
            $resp[] = $r;
        }
        return $resp;
    }

}
