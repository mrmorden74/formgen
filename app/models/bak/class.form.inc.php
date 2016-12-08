<?php
//Klasse zur Darstellung der Formulare
class Form {
    //speicert die Methode vom Formular (POST oder GET)
    private $method = '';
    //Konstruktor 
    public function __construct(string $meth) {
       //die Methode zum Setzen von $method wird aufgerufen
        $this->setMethod($meth);
    }
    //setzt die Variable $method
    private function setMethod(string $meth) {
        if(($meth === 'POST') || ($meth === 'GET')) {
            $this->method = $meth;
        }
    }
    //liefert die gesetze Methode fuer das Formular
    private function getMethod() : string {
        return $this->method;
    }
    //erzeugt ein Formular
    public function createForm() : string {
        return "<form method=\"{$this->getMethod()}\" action=\"\" class=\"pure-form pure-form-aligned\">\n<fieldset>\n"; 
    }
    //erzeugt ein Label
    public function addLabel(string $fieldID, string $labelValue) : string {
        return "<label for=\"$fieldID\">$labelValue</label>\n";
    }
    //erzeugt die jeweiligen Input-Felder
    public function addFormField(string $fieldType, string $fieldName, string $fieldID, array $arg) : string {

        switch($fieldType) {
            case 'password' :
            case 'email'    :
            case 'text'     : return "<input type=\"$fieldType\" name=\"$fieldName\" id=\"$fieldID\" maxlength=\"$arg[0]\" value=\"$arg[1]\" required>\n";
            case 'submit'   : return "<input type=\"$fieldType\" name=\"$fieldName\" id=\"$fieldID\" value=\"$arg[0]\" class=\"pure-button pure-button-primary\">\n";
            case 'radio'    : return "<input type=\"$fieldType\" name=\"$fieldName\" id=\"$fieldID\" value=\"$arg[0]\" $arg[1]>$arg[0]\n";
            default         : return "";
        }
    }
    //endet das Formular
    public function endForm() : string {
        return "</fieldset>\n</form>\n";
    }
}
?>