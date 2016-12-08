
<?php 
//Klasse fuer die Verbindung zur Datenbank
class DB {
    private $host = ''; //host
    private $user = ''; //user
    private $pw   = ''; //pw
    private $database = ''; //db
    private $table    = ''; //tabelle
    //Konstruktor: host, user, pw, db und tabelle werden gesetzt
    public function __construct() {
        $this->setHost('localhost');
        $this->setUser('root');
        $this->setPW();
        $this->setDB('kurse');
        $this->setTable('kunden');
    }
    //Methode zur Festlegung des Host
    private function setHost(string $host='') {
        if($host !== '') {
            $this->host = $host;
        }
    }
    //liefert den Host zurueck
    private function getHost() : string {
        return $this->host;
    }
    //Methode zur Festlegung des Users
    private function setUser(string $user='') {
        if($user !== '') {
            $this->user = $user;
        }
    }
    //liefert den User zurueck
    private function getUser() : string {
        return $this->user;
    }
    //Methode zur Festlegung vom Passwort
    private function setPW(string $pw='') {
        if($pw !== '') {
            $this->pw = $pw;
        }
    }
    //liefert das Passwort zurueck
    private function getPW() : string {
        return $this->pw;
    }
    //Methode zur Festlegung der Datenbank
    private function setDB(string $database='') {
        if($database !== '') {
            $this->database = $database;
        }
    }
    //liefert die DB zurueck
    private function getDB() : string {
        return $this->database;
    }
    //Methode zur Festlegung der Tabelle
    private function setTable(string $table='') {
        if($table !== '') {
            $this->table = $table;
        }
    }
    //liefert die Tabelle zurück
    public function getTable() : string {
        return $this->table;
    }
    //Methode zur Verbindung zur Datenbank
    public function connectDB() : mysqli {
        return mysqli_connect($this->getHost(), $this->getUser(), $this->getPW(), $this->getDB());
    }
}

?>