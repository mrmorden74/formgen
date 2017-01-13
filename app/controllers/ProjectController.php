<?php

class ProjectController extends Controller {
   function render() {

    $srv = new SrvList($this->db);
    for ($srv->all(); !$srv->dry(); $srv->next()){
        $datasrv[] = $srv->cast();
    }
    if(count($datasrv)>0) {
        foreach ($datasrv as $key => $value) {
            $srvname[$value['id']] = $datasrv[$key];
            $this->f3->set('srvs',$srvname);
        }   
    }
    $db = new DbList($this->db);
    $count = 0;
    $data = [];
    
        for ($db->all(); !$db->dry(); $db->next()){
            $data[] = $db->cast();
            $data[$count]['server'] = $srvname[$data[$count]['srvlist_id']]['server']; 
            $data[$count]['username'] = $srvname[$data[$count]['srvlist_id']]['username']; 
            $data[$count]['password'] = $srvname[$data[$count]['srvlist_id']]['password']; 
            if ($conn = create_con (
                $data[$count]['server'], 
                $data[$count]['username'], 
                $data[$count]['password'])) {
            $data[$count]['tables'] = show_tables ($conn,$data[$count]['dbname'])->num_rows ?? 0;
            }
            $count ++;
        }
    
    $this->f3->set('dataFromDb',$data);

    if(!$db->dry()) {
        $valid=[];
        $valid[]= "No Project exist!"; 
    }
    
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','projectBase.html');
        echo $template->render('base.html');
    var_dump($srvname);
    var_dump($data);    
    }   
    
    function addPrjUsr() {
        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'srvlist_id' => 'required|alpha_numeric',
            'dbname' => 'required|alpha_numeric',
            'projectname' => 'required|alpha_numeric',
        ));

        if($valid === true) {
            // continue
        } else {
            $this->f3->set('validprj',$valid);
            $id['id'] = $data['id'];
            $this->render(); 
            exit;
        }
        $srv = new SrvList($this->db);
        $srv->getById($data['srvlist_id']);
        if($srv->dry()) {
            $valid=[];
            $valid[]= "No login information stored"; 
            $this->render(); 
            exit;
        }
        if ($conn = create_con ($srv->server, $srv->username, $srv->password)) {
            // TODO NAchfragen wenn DB schon existiert. Verwenden o. Abbrechen
            if ($conn = create_db ($conn, $data['dbname'])) {
                $user = new DbList($this->db);
                $user->srvlist_id = $data['srvlist_id'];
                $user->dbname = $data['dbname'];
                $user->projectname = $data['projectname'];
                $user->active = 1;
                $user->save();
                $conn = close_dbcon ($conn);
                $this->f3->reroute('/');
            }
        }
            $id['id'] = $data['id'];
            $this->render(); 
            exit;
    }
    
    function showFrms ($f3,$params) {
        // echo $params['id'];
        $srv = new SrvList($this->db);
        // $srv->getById($params['srvid']);
        $srv->load(array('id=?',$params['srvid']));
        $srv->copyTo('POST');
        if($srv->dry()) {
            $valid=[];
            $valid[]= "No login information stored"; 
            $this->f3->reroute('/showFrms/'.$params['srvid'].'/'.$params['id']); 
            exit;
        }

        $db = new DbList($this->db);
        $db->getById($params['id']);
        if($db->dry()) {
            $valid=[];
            $valid[]= "No login information stored"; 
            $this->f3->reroute('/showFrms/'.$params['srvid'].'/'.$params['id']);
            exit;
        }

    $tbl = new TblList($this->db);
    for ($tbl->load(array('dbid=?',$params['id'])); !$tbl->dry(); $tbl->next()){

        $datatbl[] = $tbl->cast();
    }

    for ($srv; !$srv->dry(); $srv->next()){
        $datasrv[] = $srv->cast();
    }
    for ($db; !$db->dry(); $db->next()){
        $datadb[] = $db->cast();
    }
    
        $this->f3->set('srvdata',$datasrv);
        $this->f3->set('dbdata',$datadb);
        $this->f3->set('tbldata',$datatbl);


        $conn = create_con ($datasrv[0]['server'], $datasrv[0]['username'], $datasrv[0]['password'], $datadb[0]['dbname']);
        $result = show_tables($conn);
        while ($row = $result->fetch_assoc()) {
            $tables[$row['Tables_in_'.$datadb[0]['dbname']]] = [];
            // $tables[$row['Tables_in_'.$datadb[0]['dbname']]] =
        }
        if(count($datatbl)>0) {
            foreach ($datatbl as $key => $value) {
                $tables[$value['tablename']] = $datatbl[$key];
                $this->f3->set('tbls',$tblname);
            }   
        }
        $this->f3->set('dataFromDb',$tables);

        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','formBase.html');
        echo $template->render('base.html');
        echo '$tables';
        var_dump($tables);
        var_dump($datatbl);    
    }

    function addFrm () {
        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'dblist_id' => 'required|alpha_numeric',
            'srvlist_id' => 'required|alpha_numeric',
            'tablename' => 'required|alpha_numeric',
            'formname' => 'required|alpha_numeric',
        ));
        echo 'tets';
        if($valid === true) {
            // continue
            // var_dump ($data);
            $tbl = new TblList($this->db);
            $tbl->dbid = $data['dblist_id'];
            $tbl->tablename = $data['tablename'];
            $tbl->formname = $data['formname'];
            $tbl->save();   
        } else {
            $this->f3->set('validfrm',$valid);
            $id['id'] = $data['id'];
        }
        $params['srvid'] = $data['srvlist_id'];
        $params['id'] = $data['dblist_id'];
        $this->showFrms($f3,$params); 
    }

    function delFrm ($f3,$params) {
        var_dump($params);
        $tbl = new TblList($this->db);
        $tbl->delete($params['id']);
        $this->f3->reroute('/showFrms/'.$params['srvid'].'/'.$params['dbid']);
    }



    function createFrm ($f3,$params) {
        $tbl = new TblList($this->db);
        for ($tbl->getById($params['id']); !$tbl->dry(); $tbl->next()){
            $datatbl[] = $tbl->cast();
        }
        $formname = $datatbl[0]['formname'];
        $object[$formname] = $datatbl[0];
        $db = new DbList($this->db);
        for ($db->getById($datatbl[0]['dbid']); !$db->dry(); $db->next()){
            $datadb[] = $db->cast();
        }
        $srv = new SrvList($this->db);
        for ($srv->getById($datadb[0]['srvlist_id']); !$srv->dry(); $srv->next()){
            $datasrv[] = $srv->cast();
        }
        $object[$formname]['projectname'] = $datadb[0]['projectname'];
        $object[$formname]['srvlist_id'] = $datadb[0]['srvlist_id'];
        if ($conn = create_con ($datasrv[0]['server'], $datasrv[0]['username'], $datasrv[0]['password'], $datadb[0]['dbname'])) {
            $sql="SHOW FIELDS FROM ".$datatbl[0]['tablename'];
            // $sql="DESCRIBE ".$datatbl[0]['tablename'];
            if (!($result=mysqli_query($conn,$sql))) {
                $valid[]= "Error3: ". $conn->error; 
                var_dump ($valid);
                exit;
            }
            while ($result->fetch_assoc()) {
                foreach ($result As $key => $value) {
                    $columns[$key] = $value;
                }
                $countid=0;
                foreach ($columns As $key => $value) {
                    //  echo $key;
                    // var_dump($value);
                    //  $columnnames[] = $value['Field'];
                    $object[$formname]['fields'][$value['Field']] = $value;
                    $object[$formname]['fields'][$value['Field']]['id'] = $countid;
                    $countid++;
                }
            }


            //foreign keys ermittel und $object den Fields hinzufügen
            $sql="select
                concat(table_name, '.', column_name) as 'foreign key', 
                concat(referenced_table_name, '.', referenced_column_name) as 'references'
                    from
                information_schema.key_column_usage
                    where
                referenced_table_name is not null
                and table_schema = '".$datadb[0]['dbname']."'";
            if (!($result2=mysqli_query($conn,$sql))) {
                $valid[]= "Error3: ". $conn->error; 
                // var_dump ($valid);
                exit;
            }
            while ($result2->fetch_assoc()) {
                foreach ($result2 As $key => $value) {
                    $foreigntmp[$key] = $value;
                }
            }
            foreach ($foreigntmp As $key => $value) {
                $foreignsplit = explode(".",$value['foreign key']);
                $referencesplit = explode(".",$value['references']);
                $foreign[$foreignsplit[0]]['table'] = $foreignsplit[0];
                $foreign[$foreignsplit[0]]['field'] = $foreignsplit[1];
                $foreign[$foreignsplit[0]]['reftable'] = $referencesplit[0];
                $foreign[$foreignsplit[0]]['reffield'] = $referencesplit[1];
                if ($foreignsplit[0] == $datatbl[0]['tablename']) {
                    $object[$formname]['fields'][$foreignsplit[1]]['reference']['reftable'] = $referencesplit[0];
                    $object[$formname]['fields'][$foreignsplit[1]]['reference']['reffield'] = $referencesplit[1];
                    
                    $sql="SHOW FIELDS FROM ".$referencesplit[0];
                    if (!($resultref=mysqli_query($conn,$sql))) {
                        $valid[]= "Error3: ". $conn->error; 
                        var_dump ($valid);
                        exit;
                    }
                    while ($resultref->fetch_assoc()) {
                        foreach ($resultref As $key => $value) {
                            $columnsref[$key] = $value;
                        }
                        foreach ($columnsref As $key => $value) {
                            //  echo $key;
                            // var_dump($value['Field']);
                            $object[$formname]['fields'][$foreignsplit[1]]['reference']['reffields'][$value['Field']] = $value;
                            //  $columnnames[] = $value['Field'];
                            // $object[$formname]['fields'][$value['Field']] = $value;
                        }
                    }
                }
            }
            /* default wert zusammensetzen
            TODO: 
                Autowertform: auto_increment, Fixwert, Von Tabelle ...[Alle Tabellen der TB]
                Inhalt: auto_increment, Felder zur Tabelle (Java), Wert.
            aus Default, Extra (auto_increment) und foreign_key
            */
            foreach ($object[$formname]['fields'] as $field) {
                $values[$field['Field']] = $field['Default'];
                if ($field['Default']) {
                $object[$formname]['fields'][$field['Field']]['auto'][] = $field['Default'];
                }
                if ($field['Extra']) {
                $object[$formname]['fields'][$field['Field']]['auto'][] = $field['Extra'];
                }
                if ($field['reference']) {
                    foreach ($field As $ref => $value) {
                        if ($ref='reference' && is_array($value)) {
                            foreach ($value As $ref2 => $value2) {
                                if ($ref2='reffields' && is_array($value2)) {
                                    foreach ($value2 As $ref3 => $value3) {
                                        // echo $ref3.'<br>';
                                        $object[$formname]['fields'][$field['Field']]['auto'][] = '['.$ref3.']';
                            }
                        }
                            }
                        }
                    }
                }
                
            }
        $this->f3->set('fields',$columns);
        $this->f3->set('object',$object);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','formFields.html');
        echo $template->render('base.html');
            ini_set('xdebug.var_display_max_depth', '10');
            var_dump($datadb);
            echo '$object';
            var_dump($object);

        /* TODO: add Field
        */

        }
    }

    function saveFrm ($f3,$params) {
            ini_set('xdebug.var_display_max_depth', '10');
        $tbl = new TblList($this->db);
            for ($tbl->getById($params['id']); !$tbl->dry(); $tbl->next()){
            $datatbl[] = $tbl->cast();
            }
        $db = new DbList($this->db);
            for ($db->getById($datatbl[0]['dbid']); !$db->dry(); $db->next()){
            $datadb[] = $db->cast();
            }
        $srv = new SrvList($this->db);
            for ($srv->getById($datadb[0]['srvlist_id']); !$srv->dry(); $srv->next()){
            $datasrv[] = $srv->cast();
            }
        // echo '$datatbl';
        // var_dump($datatbl);
        // echo '$datadb';
        // var_dump($datadb);
        // echo '$datasrv';
        // var_dump($datasrv);
        // echo '$params';
        // var_dump($params);
        
        $data = $this->f3->get('POST');
        var_dump($data);
        $valid = Validate::is_valid($data[0], array(
            'fieldname' => 'alpha_numeric',
        ));

        if($valid === true) {
            // continue
        } else {
            $this->f3->set('validfrm',$valid);
            $id['id'] = $data['id'];
            // var_dump($valid);
            // $this->createFrm($f3, $id); 
            exit;
        }

  		$form = new FrmList($this->db);
        foreach ($data As $key => $value) {
            // echo $key.'='.$value.'<br>';
            if(is_array($value)) {
            $form->tbllist_id = $params['id'];
            $form->field_id = $key;
            $form->tbl_fieldname = $value['tbl_fieldname'];
                // if ($value[1] == ''){
                //     $value[1] = $value[0];
                // }       
            $form->fieldname = $value['fieldname'];
            $form->type = $value['type'];
            $form->empty = $value['empty'];
            $form->field_key = $value['field_value'];
            $form->sort = $value['sort'];
            $form->autowert = $value['autowert'];
            $form->field_hide = $value['field_show'];
            $form->save();
            $form->reset();
            }
        }
        //create and export config
        $export['tblname'] = $data['tblname'];
        $export['frmname'] = $data['frmname'];
        foreach($data as $key => $value) {
            if(intval($key) || $key === 0) {
                // TODO: Variable für Variablennamen zur Codeverkürzung
                // $field = "export['field']['".$value['tbl_fieldname']."']['fieldType']";
                // echo $field."<br>";               
                $export['fields'][$value['tbl_fieldname']]['fieldType'] = $this->setFieldType($value);
                $export['fields'][$value['tbl_fieldname']]['label'] = $this->setFieldName($value);
                $export['fields'][$value['tbl_fieldname']]['dbName'] = $value['tbl_fieldname'];
                $export['fields'][$value['tbl_fieldname']]['dataType'] = $this->setDataType($value);
                $export['fields'][$value['tbl_fieldname']]['required'] = $this->setRequired($value);
                $export['fields'][$value['tbl_fieldname']]['placeholder'] = '';
                $export['fields'][$value['tbl_fieldname']]['preFix'] = '';
                $export['fields'][$value['tbl_fieldname']]['minVal'] = 0;
                $export['fields'][$value['tbl_fieldname']]['maxVal'] = 0;
                $export['fields'][$value['tbl_fieldname']]['formatText'] = '';
                $export['fields'][$value['tbl_fieldname']]['autoValue'] = $this->setAutoValue($value);
                $export['fields'][$value['tbl_fieldname']]['edit'] = true;
            }
        }

        $path = $this->f3->get('ROOT');
        $path .= '\\formgen\\'.$datadb[0]['projectname'].'\\'.$datatbl[0]['formname'];
        $filename = $datatbl[0]['formname'];
        $format = 'json';
        export_file ($filename,$path,$export,$format);
        // $this->f3->reroute('/createFrm/'.$params['id']);
    }
    function setFieldType($data) {
        // varchar -> text
        $value = 'text';
        // int -> number
        if (stristr($data['type'], 'int')||stristr($data['type'], 'decimal')) {
            $value = 'number';
        }
        // type = date -> Kalender
        if ($data['type'] == 'date') {
            $value = 'date';
        }
        // bool -> checkbox
        if ($data['type'] == 'tinyint(1)') {
            $value = 'checkbox';
        }
        // Autowert = [] -> select
        if (stristr($data['Autowert'], '[')) {
            $value = 'select';
        }
        return $value;
    }
    function setDataType($data) {
        $value = 'text';
        if (stristr($data['type'], 'int')) {
            $value = 'number';
        }
        if (stristr($data['type'], 'decimal')) {
            $value = 'float';
        }
        // type = date -> Kalender
        if ($data['type'] == 'date') {
            $value = 'date';
        }
        //email, regex
        return $value;

    }
    function setFieldName($data) {
        $value = $data['fieldname'];

        if ($data['fieldname'] == '') {
            $value = $data['tbl_fieldname'];
        }
        return $value;

    }
    function setRequired($data) {
        $value = false;
        if ($data['empty'] == 'NO') {
            $value = true;
        }

        return $value;

    }
    function setAutoValue($data) {
        $value = '';
        if (!stristr($data['Autowert'], '[')) {
            $value = $data['Autowert'];
        }
        return $value;
    }

    function setXXX($data) {
        $value = 'text';

        return $value;

    }
}