<?php
	// Formularfelder einfügen
	$formFields = makeFormFields($formConfig,$formErrors,'insert');
	echo $formFields;
?>
<p class="error"><?php echo $errorMsg; ?></p>
<a href="index.php">zurück zur Übersicht</a>
	
