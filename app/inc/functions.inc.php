<?php

function predump ($var) {

    var_dump($var);
}

    /** 
    * 
    *
    **/
    function getTblIdFromFrmID($id) {
        $tbl = new TblList($this->db);
        for ($tbl->getById($params['id']); !$tbl->dry(); $tbl->next()){
            $datatbl[] = $tbl->cast();
        }
        var_dump($datatbl);
        return $datatbl[0];
    }