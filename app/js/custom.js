function rescue_checkbox_state(id) {
  var checkbox = document.getElementById(id);
  if (checkbox.checked != true) {
    checkbox.checked = true;
  } else {
    checkbox.checked = false;
  }
}
    
function msgBox() { 
  var box=window.confirm("Wollen Sie weiter Surfen zu....") // textangebe die mit der confirm-box ausgegeben wird. 
  if(box==true){ 
    window.location.href="http://www.googel.de"; // www-addressenangabe fuer wenn OK gedrückt wird. 
  } 
  else if(box==false){ 
    alert("schade"); // ausgabe für wenn abbrechen gedrückt wird. 
  } 
} 
