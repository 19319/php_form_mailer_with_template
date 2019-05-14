<?php

// -------------------------------------------------------------------
// Vendor
// -------------------------------------------------------------------
require '../vendor/phpShowErrors/phpShowErrors.php';
require '../vendor/MyCollection/MyCollection.php';
require '../vendor/MyFormfield/MyFormfield.php';


// -------------------------------------------------------------------
// Create a collection of form fields
// -------------------------------------------------------------------
$fields = new MyCollection();

// -------------------------------------------------------------------
// Create a form field object and add it to the collection
// -------------------------------------------------------------------
$key = 'salutation';
$label = 'Salutation';
$options = array();
$options['type'] = 'radio';
$options['id'] = $key;
$options['css'] = '';
$options['style'] = '';
$options['name'] = $key;
$options['label'] = $label;
$options['choices'] = array();
$options['choices'][0]['name'] = 'Mr.';
$options['choices'][0]['value'] = 'Mr.';
$options['choices'][1]['name'] = 'Mrs.';
$options['choices'][1]['value'] = 'Mrs.';
//$options['choices'][2]['name'] = 'Divers';
//$options['choices'][2]['value'] = 'Divers';
$options['required'] = true;
$fields->add( $key, new MyFormfield($options) );

// -------------------------------------------------------------------
// Create a form field object and add it to the collection
// -------------------------------------------------------------------
$key = 'name';
$label = 'Name';
$options = array();
$options['type'] = 'text';
$options['id'] = $key;
$options['css'] = '';
$options['style'] = '';
$options['name'] = $key;
$options['label'] = $label;
$options['required'] = true;
$fields->add( $key, new MyFormfield($options) );

// -------------------------------------------------------------------
// Create a form field object and add it to the collection
// -------------------------------------------------------------------
$key = 'submit';
$label = '';
$options = array();
$options['type'] = 'submit'; // text|radio|submit
$options['id'] = $key;
$options['css'] = '';
$options['style'] = '';
$options['name'] = $key;
$options['value'] = 'Do me'; // Beschriftung des Buttons
$options['label'] = $label;
$fields->add( $key, new MyFormfield($options) );

// -------------------------------------------------------------------
// Form template
// -------------------------------------------------------------------
$templateForm = '
<form class="backgroundTemplate1" action="index.php" method="post">

	<div>
	###salutationLabel###<br>
	###salutation###
	###salutationErrormessages###
	</div>

	<div>
	###nameLabel###<br>
	###name###
	###nameErrormessages###
	</div>

	###submit###

</form>
';

// -------------------------------------------------------------------
// Form template
// -------------------------------------------------------------------
$templateThankYou = '
<h2>Thx for your message</h2>

<div class="backgroundTemplate2">

	<div style="padding-bottom: 1rem;">
	###salutationLabel###<br>
	<b>###salutationValue###</b>
	</div>

	<div style="padding-bottom: 1rem;">
	###nameLabel###<br>
	<b>###nameValue###</b>
	</div>

</div>
';


// -------------------------------------------------------------------
// Mail template
// -------------------------------------------------------------------
$templateMail = '
User input:

###salutationLabel###
###salutationValue###

###nameLabel###
###nameValue###

';


// -------------------------------------------------------------------
// Versandfehler
// -------------------------------------------------------------------
$templateMailError = '
<h2>Error</h2>
<div>Could not mail the form</div>
<pre>###mailErrormessage###</pre>

';


if ($fields->formIsComplete())
{

	// -------------------------------------------------------------------
	// Set to, from, subject, message
	// -------------------------------------------------------------------
	$options = array();
	$options['to'  ] = 'surfer@example.com';
	$options['from'] = 'formular@example.com';
	$options['subject'] = 'Your form';
	$options['message'] = $fields->fillTemplate($templateMail);

	// -------------------------------------------------------------------
	// Debug MAIL
	// -------------------------------------------------------------------
	if (0)
	{
		echo '<h3>Debug Mail</h3>';
		echo '<pre style="background-color:#FFF;color:NAVY;">' . print_r($options, TRUE) . '</pre>';
	}

	// -------------------------------------------------------------------
	// Send mail
	// -------------------------------------------------------------------
	$success = $fields->sendMail($templateMail, $options);
	if ($success) {

		// -------------------------------------------------------------------
		// THX FOR YOUR MESSAGE
		// -------------------------------------------------------------------
		// Screenmessage
		// -------------------------------------------------------------------
		echo $fields->fillTemplate($templateThankYou);

	} else {

		echo $fields->fillTemplate($templateMailError);

	}

} else {

	// -------------------------------------------------------------------
	// Show the form
	// -------------------------------------------------------------------
	echo $fields->fillTemplate($templateForm);

}

// -------------------------------------------------------------------
// Debug FORM
// -------------------------------------------------------------------
if (0)
{
	echo '<h3>Debug Form</h3>';
	echo '<pre>';
	echo htmlspecialchars($fields->fillTemplate($templateForm));
	echo '</pre>';
}

// -------------------------------------------------------------------
// Debug 2
// -------------------------------------------------------------------
if (0) {

    //header('Content-Type: text/html; charset=utf-8');

    $dbg = array();
    $dbg['FILE'] = basename(__FILE__);
    $dbg['FUNCTION'] = __FUNCTION__;
    $dbg['LINE'] = __LINE__;
    $dbg['MEMORY_NOW'] = number_format(memory_get_usage(TRUE) / (1024 * 1000), 1) . ' MB';
    $dbg['MEMORY_PEAK'] = number_format(memory_get_peak_usage(TRUE) / (1024 * 1000), 1) . ' MB';

    $dbg['STATUS'][] = '';
    $dbg['_POST'] = $_POST;
    //$dbg['INPUT']['$fields'] = $fields;
    $dbg['COMPUTED']['$fields->getFirst()'] = $fields->getFirst();
	//$dbg['COMPUTED']['Num items'] = $fields->count();
	$dbg['COMPUTED']['getErrormessages'] = $fields->getFirst()->getErrormessages();
	$dbg['COMPUTED']['getErrormessagesAsHtml'] = $fields->getFirst()->getErrormessagesAsHtml();
	$dbg['COMPUTED']['$options'] = $options;

    $dbgmsg = '<pre style="background-color:#FFF;color:NAVY;">' . print_r($dbg, TRUE) . '</pre>';
    die($dbgmsg);
    //error_log('DEBUG:  '. print_r($dbg, TRUE) . __FUNCTION__ .', LINE '. __LINE__);

} //eoIf(0)
