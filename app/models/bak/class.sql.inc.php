<?php
//Klasse fuer SQL
class SQL {
    //Methode fuer SQL-Statements
    public function setQuery(string $sql, mysqli $res) {
        return $res->query($sql);
    }
    //liefert die Anzahl der Reihen, die eingefuegt wurden
    public function getAffectedRows(mysqli $res) {
        return $res->affected_rows;
    }
    //liefert die Anzahl der Ergebnisse
    public function getNumRows(mysqli_result $ress) : int {
        return $ress->num_rows;
    }
    //Methode zur Festlegung der neuenKundennummer
    public function getNewCustomerNumber(string $sql, mysqli $res) : string {
        if($result = $this->setQuery($sql, $res)) {
            $nRow = $this->getNumRows($result);
            if($nRow === 1) {
                $neueKundenNummer = '';
                $kn = $result->fetch_assoc();
                $nummer = $kn['max'];
                $neuerWert = str_replace('KdNr-', '', str_replace('0', '', $nummer));
                $neuerWert++;
                $diff = 6 - strlen($neuerWert);
                for($i=0;$i<$diff;$i++) {
                    $neueKundenNummer .= '0';
                }
                $neueKundenNummer .= $neuerWert;
            return $neueKundenNummer;
            }
            else {
                return '000001';
            }
        }
        return '';
    }
}
?>