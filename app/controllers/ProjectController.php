<?php
/**
*   Child class des Controller(f3)
*   Steuert alle Projektfunktionen
*/
class ProjectController extends Controller {

    /**
    *  Rendert Hauptseite Projektauswahl
    *  return render projectBase.html
    */
    function render() {

        // Liest Tabelle SrvList aus und speichert Servernamen
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

        // Liest DBList aus und erzeugt Liste der Projekte
        $db = new DbList($this->db);
        $count = 0;
        $data = [];
        
            for ($db->all(); !$db->dry(); $db->next()){
                $data[] = $db->cast();
                $data[$count]['server'] = $srvname[$data[$count]['srvlist_id']]['server']; 
                $data[$count]['username'] = $srvname[$data[$count]['srvlist_id']]['username']; 
                $data[$count]['password'] = $srvname[$data[$count]['srvlist_id']]['password']; 
                $pw = $this->decrypt($data[$count]['password']);
                // Liefert Anzahl der vorhandenen Tabellen
                if ($conn = create_con (
                    $data[$count]['server'], 
                    $data[$count]['username'], 
                    $pw)) {
                $data[$count]['tables'] = show_tables ($conn,$data[$count]['dbname'])->num_rows ?? 0;
                }
                // Liefert Anzahl der gespeicherten Formulare
                // TODO:
                $DbId = $data[$count][id];
                $tbl = new TblList($this->db);
                for ($tbl->load(array('dbid=?',$DbId)); !$tbl->dry(); $tbl->next()){
                    $datatbl[$count][] = $tbl->cast();
                }
                $data[$count]['forms'] = count($datatbl[$count]);

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
    
    /**
    *  Validiert und fügt Projekt zur DbList hinzu
    *  return render	render
    */
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
    
    /**
    *  Rendert Liste der Tabellen bzw. Formulare
    *  param $f3	object 	Fatfree Opjekt
    *  param $params	array	Array mit den GET Paarmetern
    *  return render	formbase.html
    */
    function showFrms ($f3,$params) {
        // Kontrolliert ob für die Datenbank Zugangsdaten gespeichert sind
        $srv = new SrvList($this->db);
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
        // Holt zugehörige Tabellendaten
        $tbl = new TblList($this->db);
        for ($tbl->load(array('dbid=?',$params['id'])); !$tbl->dry(); $tbl->next()){
            $datatbl[] = $tbl->cast();
        }
        // Holt zugehörige Serverdaten
        for ($srv; !$srv->dry(); $srv->next()){
            $datasrv[] = $srv->cast();
        }
        // Holt zugehörige Datenbankdaten
        for ($db; !$db->dry(); $db->next()){
            $datadb[] = $db->cast();
        }
        $this->f3->set('srvdata',$datasrv);
        $this->f3->set('dbdata',$datadb);
        $this->f3->set('tbldata',$datatbl);
        //Richtet die Datenbankverbindung und liest Tabellen aus
        $pw = $this->decrypt($datasrv[0]['password']);
        // echo $datasrv[0]['server'], $datasrv[0]['username'], $pw, $datadb[0]['dbname'];
        $conn = create_con ($datasrv[0]['server'], $datasrv[0]['username'], $pw, $datadb[0]['dbname']);
        $result = show_tables($conn);
        while ($result->fetch_assoc()) {
            foreach ($result as $key => $row) {
            $tables[$row['Tables_in_'.$datadb[0]['dbname']]] = [];
            }
        }
        if(count($datatbl)>0) {
            foreach ($datatbl as $key => $value) {
                $tables[$value['tablename']] = $datatbl[$key];
                $this->f3->set('tbls',$tblname);
            }   
        }
        $this->f3->set('dataFromDb',$tables);
        // render
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','formBase.html');
        echo $template->render('base.html');
        //DEBUG
        echo '$tables';
        var_dump($tables);
        var_dump($datafrm);    
    }

    /**
    *  Validiert und Speichert Formularname
    *  return route	showFrms
    */    
    function addFrm () {
        $data = $this->f3->get('POST');
        $valid = Validate::is_valid($data, array(
            'dblist_id' => 'required|alpha_numeric',
            'srvlist_id' => 'required|alpha_numeric',
            'tablename' => 'required|alpha_dash',
            'formname' => 'required|alpha_dash',
        ));
        if($valid === true) {
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
        // TODO: (leere) Tabelle erzeugen
    }

    /**
    *  Validiert und Ändert Formularname
    *  return route	showFrms
    */    
    function editFrm () {
        $data = $this->f3->get('POST');
        var_dump($_POST);
        $valid = Validate::is_valid($data, array(
            'tablename' => 'required|alpha_dash',
            'formname' => 'required|alpha_dash',
        ));
        if($valid === true) {
            $tbl = new TblList($this->db);
            $tbl->getByName($data['tablename']);
            $tbl->copyFrom('POST');
            $tbl->update();   
        } else {
            $this->f3->set('validfrm',$valid);
            $id['id'] = $data['id'];
        }
        $params['srvid'] = $data['srvlist_id'];
        $params['id'] = $data['dblist_id'];
        $this->showFrms($f3,$params); 
        // // TODO: (leere) Tabelle erzeugen
    }
    /**
    *  Löscht Formulareintrag
    *  param $f3	object 	Fatfree Opjekt
    *  param $params	array	Array mit den GET Paarmetern
    *  return reroute	showFrms
    */ 
    function delFrm ($f3,$params) {
        var_dump($params);
        $tbl = new TblList($this->db);
        $tbl->delete($params['id']);
        $this->f3->reroute('/showFrms/'.$params['srvid'].'/'.$params['dbid']);
    }

    /**
    *  Rendert Formular zur Formulardefinition
    *  param $f3	object 	Fatfree Opjekt
    *  param $params	array	Array mit den GET Paarmetern
    *  return reroute	formFields.html
    */ 
    function createFrm ($f3,$params) {
        // Daten aus allen Tabellen einlesen
        $tbl = new TblList($this->db);
            for ($tbl->getById($params['id']); !$tbl->dry(); $tbl->next()){
                $datatbl[] = $tbl->cast();
            }
            $formname = $datatbl[0]['formname'];
            // $object mit Infos aus TblList befüllen
            $object[$formname] = $datatbl[0];
        $db = new DbList($this->db);
            for ($db->getById($datatbl[0]['dbid']); !$db->dry(); $db->next()){
                $datadb[] = $db->cast();
            }
        $srv = new SrvList($this->db);
            for ($srv->getById($datadb[0]['srvlist_id']); !$srv->dry(); $srv->next()){
                $datasrv[] = $srv->cast();
            }
        //$object mit Projektname und ServerID ergänzen
        $object[$formname]['projectname'] = $datadb[0]['projectname'];
        $object[$formname]['srvlist_id'] = $datadb[0]['srvlist_id'];
        // Alle Tabellenfelder der Zieltabelle auslesen
        // und an $object als fields anfügen
        // Zusätzlich Counter als ID für Tabellenfelder einfügen
        $pw = $this->decrypt($datasrv[0]['password']);
        if ($conn = create_con ($datasrv[0]['server'], $datasrv[0]['username'], $pw, $datadb[0]['dbname'])) {
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
            //foreign keys ermittel und $object den Fields asl refernce hinzufügen
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
            /* default wert zusammensetzen und zu fields.auto anfügen
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
        // Daten aus FrmList auslesen und in $object einbauen
        $frm = new FrmList($this->db);
            for ($frm->load(array('tbllist_id=?',$params['id'])); !$frm->dry(); $frm->next()){
                $datafrm[] = $frm->cast();
            }
        foreach ($datafrm as $key => $value) {
            $object[$formname]['fields'][$value['tbl_fieldname']]['fieldname'] = $value['fieldname'];
            $object[$formname]['fields'][$value['tbl_fieldname']]['frmId'] = $value['id'];
            //$value['field_id'];
            $object[$formname]['fields'][$value['tbl_fieldname']]['type'] = $value['type'];
            $object[$formname]['fields'][$value['tbl_fieldname']]['empty'] = $value['empty'];
            $object[$formname]['fields'][$value['tbl_fieldname']]['field_key'] = $value['field_key'];
            $object[$formname]['fields'][$value['tbl_fieldname']]['autowert'] = explode(",",$value['autowert']);
            $object[$formname]['fields'][$value['tbl_fieldname']]['sort'] = $value['sort'];
            $object[$formname]['fields'][$value['tbl_fieldname']]['field_hide'] = $value['field_hide'];
        }

        // render
        $this->f3->set('object',$object);
        $template=new Template;
        $this->f3->set('header','header.html');
        $this->f3->set('content','formFields.html');
        echo $template->render('base.html');
        //DEBUG
            ini_set('xdebug.var_display_max_depth', '10');
            echo '$datadb';
            var_dump($datadb);
            echo '$datafrm';
            var_dump($datafrm[4]);
            echo '$object';
            var_dump($object);

        /* TODO: add Field
        */

        }
    }

    /**
    *  Speichert Formulardefinition und Exportiert $formConfigAll[]
    *  param $f3	object 	Fatfree Opjekt
    *  param $params	array	Array mit den GET Paarmetern
    *  return route	createFrm
    */ 
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
        
        $data = $this->f3->get('POST');
        // var_dump($data);
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
            $_POST = NULL;
            if(is_array($value)) {
                if($value['id']) {
                    $_POST['id'] = $value['id'];
                }
                $_POST['tbllist_id'] = $params['id'];
                $_POST['field_id'] = $key;
                $_POST['tbl_fieldname'] = $value['tbl_fieldname'];
                $_POST['fieldname'] = $value['fieldname'];
                $_POST['type'] = $value['type'];
                $_POST['empty'] = $value['empty'];
                $_POST['field_key'] = $value['field_key'];
                $_POST['sort'] = $value['sort'];
                $autowert = $value['Autowert'];
                if (is_array($value['Autowert'])) {
                $autowert = implode(",",$value['Autowert']);
                }
                $_POST['autowert'] = $autowert;
                
                $_POST['field_hide'] = $value['field_hide'];
                if($value['id']) {
                    // echo $key;
                    // var_dump($_POST);
                    $form->edit($value['id']);
                } else {
                    $form->save();
                }
                $form->reset();
            }
        }
        //create and export config
        $export['tblname'] = $data['tblname'];
        $export['frmname'] = $data['frmname'];
        $export['primary'] = '';
        
        foreach($data as $key => $value) {
            if(intval($key) || $key === 0) {
                // TODO: Variable für Variablennamen zur Codeverkürzung
                // $field = "export['field']['".$value['tbl_fieldname']."']['fieldType']";
                // echo $field."<br>";
                if ($value['field_key']=='PRI') {
                    $export['primary'] = $value['tbl_fieldname'];
                }    
                if ($value['Autowert'] != 'auto_increment' &&
                    $value['field_hide'] != 1) {
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
        }

        $root = $this->f3->get('ROOT');
        $path = $root.'\\formgen\\'.$datadb[0]['projectname'].'\\'.$datatbl[0]['formname'];
        $filename = $datatbl[0]['formname'];
        $format = 'array';
        export_file ($filename,$path,$export,$format);
        $source = $root.'\\app\\blueprints\\form';
        // TODO: wieder aktivieren DEBUG
        var_dump($_POST);
        $files = xcopy($source, $path);
        $this->f3->reroute('/createFrm/'.$params['id']);
    }

/**
*  Erzeugt Feldtyp für formConfigAll[]
*  param $data	array	Feldkonfiguration
*  return string	Html Input Type
*/
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
        // type = datetime -> Kalender
        if ($data['type'] == 'datetime') {
            $value = 'datetime';
        }
        // type = timestamp -> Kalender
        if ($data['type'] == 'timestamp') {
            $value = 'datetime';
        }
        // bool -> checkbox
        if ($data['type'] == 'tinyint(1)') {
            $value = 'checkbox';
        }
        // Autowert = [] -> select
        if (stristr($data['Autowert'][0], '[')) {
            $value = 'select';
        }
        return $value;
    }

/**
*  Erzeugt Datatyp zur Validierung für formConfigAll[]
*  param $data	array	Feldkonfiguration
*  return string	dataType zur Validierung
*/
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
        // type = datetime -> Kalender
        if ($data['type'] == 'datetime' ||
            $data['type'] == 'timestamp') {
            $value = 'datetime';
        }
        //email, regex
        return $value;

    }

/**
*  Erzeugt Feldnamen für formConfigAll[]
*  param $data	array	Feldkonfiguration
*  return string	Feldname
*/
    function setFieldName($data) {
        $value = $data['fieldname'];

        if ($data['fieldname'] == '') {
            $value = $data['tbl_fieldname'];
        }
        return $value;

    }

/**
*  Erzeugt Required für formConfigAll[]
*  param $data	array	Feldkonfiguration
*  return string	Required
*/
    function setRequired($data) {
        $value = false;
        if ($data['empty'] == 'NO') {
            $value = true;
        }

        return $value;

    }

/**
*  Erzeugt AutoValue für formConfigAll[]
*  param $data	array	Feldkonfiguration
*  return string	Autovalue
*/
    function setAutoValue($data) {
        $value = '';
        if (!stristr($data['Autowert'][0], '[')) {
            $value = $data['Autowert'];
        } else {
            $value = "[".$data['reference']."]";
            foreach($data['Autowert'] as $field)  {
                $value .= ".".$field;
            }
        }
        return $value;
    }

/**
*  Erzeugt Feldnamen für formConfigAll[]
*  param $data	array	Feldkonfiguration
*  return string	Feldname
*/
    function setXXX($data) {
        $value = 'text';

        return $value;

    }

    /**
    *  Verschlüsselt $token mit vorgebenen ENCRYPTION_KEY.
    *  twoway encryption
    *  param    $token  string  Zu verschlüsselnder String
    *  return   string  verschlüsselter Token
    */
    function encrypt($token) {
        $cryptor = new Cryptor($this->f3->get('ENCRYPTION_KEY'));
        return $cryptor->encrypt($token);
    }

    /**
    *  Entschlüsselt $token mit vorgebenen ENCRYPTION_KEY.
    *  twoway encryption
    *  param    $token  string  verschlüsselter String
    *  return   string  entschlüsselter Token
    */
    function decrypt($crypted_token) {
        $cryptor = new Cryptor($this->f3->get('ENCRYPTION_KEY'));
        // echo $encryption_key;
        return $cryptor->decrypt($crypted_token);
    }

    /**
    *  Liefer die passende ServerId zur DbListId
    *  param $DbId	int	Datenbank = Projekt Id
    *  return Int	ServerId
    */
    function getSrvIDfromDbId ($DbId) {
        $db = new DbList($this->db);
		for ($db->load(array('id=?',$DbId)); !$db->dry(); $db->next()){
            $datadb[] = $db->cast();
        }
        return $datadb[0]['srvlist_id'];
    }

}