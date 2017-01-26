function myFunction() {
   document.getElementById("demo").innerHTML = "Paragraph changed.";
} 

function OnSubmitForm()
{
  if(document.pressed == 'Insert')
  {
   document.myform.action ="insert.html";
  }
  else
  if(document.pressed == 'Update')
  {
    document.myform.action ="update.html";
  }
  alert("I am an alert box!");
  return true;
}