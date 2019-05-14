<?php

// -------------------------------------------------------------------
// Collection with SPL
// -------------------------------------------------------------------
// https://dev.to/drearytown/collection-objects-in-php-1cbk
// -------------------------------------------------------------------
class MyCollection extends \ArrayObject
{

	// -------------------------------------------------------------------
	// Mail properties
	// -------------------------------------------------------------------
	private $to;
	private $from;
	private $headers;
	private $subject;
	private $message;
	private $mailErrormessage;


	// -------------------------------------------------------------------
	// Get
	// -------------------------------------------------------------------
    public function get($key)
    {
        if (parent::offsetExists($key))
		{
        	return parent::offsetGet($key);
		}
		else
		{
			//return 'ERROR: key "'. $key .'" not found in collection';
			return '';
		}
    }

	// -------------------------------------------------------------------
	// Add
	// -------------------------------------------------------------------
	// Use this instead of append, so we can add associate arrays
    // Append only allows for numeric keys
    public function add($key, $value)
    {
		// https://www.php.net/manual/de/arrayobject.offsetset.php
        parent::offsetSet($key, $value);
    }


	// -------------------------------------------------------------------
	// First, Last, Next
	// -------------------------------------------------------------------
    public function getFirst()
    {

		// https://www.php.net/manual/de/arrayiterator.rewind.php
		$iterator = parent::getIterator();
		$iterator->rewind(); //rewinding to the begining
		$iterator->current();

		return $iterator->current();

    }

    public function getCurrent()
    {

		// https://www.php.net/manual/de/arrayiterator.rewind.php
		$iterator = parent::getIterator();

		return $iterator->current();

    }



	// -------------------------------------------------------------------
	// fillTemplate
	// -------------------------------------------------------------------
	// $template = '
	// 	<h1>My Form</h1>
	// 	<form>
	//
	// 		<div>
	// 		###anredeLabel###<br>
	// 		###anrede###
	//      ###anredeErrormessages
	// 		</div>
	//
	// 		<div>
	// 		###vornameLabel###<br>
	// 		###vorname###
	//      ###vornameErrormessages
	// 		</div>
	//
	// 		###submit###
	//
	// 	</form>
	// ';
	// -------------------------------------------------------------------
    public function fillTemplate($template='', $options=array())
    {

		if (empty($template)) {
			return ('<!-- fillTemplate: Error: Template is empty -->');
		}

		// https://www.php.net/manual/de/arrayiterator.rewind.php
		$iterator = parent::getIterator();

		//rewind to the begining
		$iterator->rewind();

		// Loop
		$html = $template;
		while($iterator->valid()) {

			// Fill template
			$html = str_replace('###'. $iterator->key() .'Label###',
								$iterator->current()->getLabelWithAsteriskIfRequired(),
								$html
								);
			$html = str_replace('###'. $iterator->key() .'###',
								$iterator->current()->generateFormfieldHtml(),
								$html
								);
			$html = str_replace('###'. $iterator->key() .'Errormessages###',
								$iterator->current()->getErrormessagesAsHtml(),
								$html
								);

			$value = $iterator->current()->getValue();
			if (empty($value))
			{
				$value = '-';
			}
			$html = str_replace('###'. $iterator->key() .'Value###',
								$value,
								$html
								);




		    $iterator->next();
		}

		// -------------------------------------------------------------------
		// Add mailErrormessage, if any
		// -------------------------------------------------------------------
		$html = str_replace('###mailErrormessage###',
							$this->getMailErrormessage(),
							$html
							);

		// -------------------------------------------------------------------
		// Return
		// -------------------------------------------------------------------
		return $html;

    }



	// -------------------------------------------------------------------
	// formIsComplete
	// -------------------------------------------------------------------
    public function formIsComplete()
    {

		// -------------------------------------------------------------------
		// Check if form was submitted
		// -------------------------------------------------------------------
		// Why? Without this check, the form would be
		// complete on first display. Because the validation is off, then.
		// -------------------------------------------------------------------
		if (is_array($_POST) && count($_POST))
		{

			// OK, form was submitted
			// Continue

		}
		else
		{

			// Not submitted
			// Stop here
			return false;

		}


		// -------------------------------------------------------------------
		// Check all fields for validator error messages
		// -------------------------------------------------------------------

		// https://www.php.net/manual/de/arrayiterator.rewind.php
		$iterator = parent::getIterator();

		//rewind to the begining
		$iterator->rewind();

		// Loop
		$numErrors = 0;
		while($iterator->valid()) {

			if ( $iterator->current()->getNumberOfErrormessages() > 0 )
			{
				$numErrors++;
			}
		    $iterator->next();
		}


		// -------------------------------------------------------------------
		// Debug
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
		    //$dbg['INPUT']['$attr'] = $attr;
		    //$dbg['INPUT']['$content'] = $content;
		    $dbg['COMPUTED']['$numErrors'] = $numErrors;
		    //$dbg['OUTPUT']['$imgSrc'] = $imgSrc;

		    $dbgmsg = '<pre style="background-color:#FFF;color:NAVY;">' . print_r($dbg, TRUE) . '</pre>';
		    die($dbgmsg);
		    //error_log('DEBUG:  '. print_r($dbg, TRUE) . __FUNCTION__ .', LINE '. __LINE__);

		} //eoIf(0)



		// -------------------------------------------------------------------
		// Return
		// -------------------------------------------------------------------
		if ($numErrors === 0)
		{
			return true;
		}

		return false;

    }


	// -------------------------------------------------------------------
	// getOption()
	// -------------------------------------------------------------------
    public function getOption($key, $options)
    {
		if (!is_array($options)) {
			return '';
		}
		if (empty($key)) {
			return '';
		}

		if (array_key_exists($key, $options)) {
			return ($options[$key]);
		} else {
			return '';
		}

    }


	// -------------------------------------------------------------------
	// mail to
	// -------------------------------------------------------------------
    public function setTo( $value )
    {
		$this->to = $value;
    }

    public function getTo()
    {
		return $this->to;
    }


	// -------------------------------------------------------------------
	// mail from
	// -------------------------------------------------------------------
    public function setFrom( $value )
    {
		$this->from = $value;
    }

    public function getFrom()
    {
		return $this->from;
    }

	// -------------------------------------------------------------------
	// mail headers/Absenderadresse
	// -------------------------------------------------------------------
    public function setHeaders( $value )
    {
		$this->headers = $value;
    }

    public function getHeaders()
    {
		return $this->headers;
    }


	// -------------------------------------------------------------------
	// mail subject
	// -------------------------------------------------------------------
    public function setSubject( $value )
    {
		$this->subject = $value;
    }

    public function getSubject()
    {
		return $this->subject;
    }


	// -------------------------------------------------------------------
	// mail message
	// -------------------------------------------------------------------
    public function setMessage( $value )
    {
		$this->message = $value;
    }

    public function getMessage()
    {
		return $this->message;
    }

	// -------------------------------------------------------------------
	// mail error message
	// -------------------------------------------------------------------
    public function setMailErrormessage( $value )
    {
		$this->mailErrormessage = $value;
    }

    public function getMailErrormessage()
    {
		return $this->mailErrormessage;
    }


	// -------------------------------------------------------------------
	// Send mail
	// -------------------------------------------------------------------
    public function sendMail($template, $options)
	{

		// -------------------------------------------------------------------
		// set to, from, subject
		// -------------------------------------------------------------------
		if (is_array($options))
		{

			$this->setTo(     	$this->getOption('to', $options) );
			$this->setFrom(   	$this->getOption('from', $options) );
			$this->setSubject(  $this->getOption('subject', $options) );
			$this->setMessage(  $this->getOption('message', $options) );

		}

		// -------------------------------------------------------------------
		// set mail headers
		// -------------------------------------------------------------------
		$headers = '';
		$headers .= 'Content-type:text/plain;charset=UTF-8' . "\r\n";
		$headers .= 'From: '. $this->getFrom() . "\r\n";

		$this->setHeaders( $headers );  // "From: formular@example.com"



		// -------------------------------------------------------------------
		// use wordwrap() if lines are longer than 70 characters
		// -------------------------------------------------------------------
		$this->setMessage(
				wordwrap($this->getMessage(),70)
		);


		// -------------------------------------------------------------------
		// Check mail data
		// -------------------------------------------------------------------
		if ( empty( $this->getTo() ) )
		{
			// Error, stop mailing
			$this->setMailErrormessage('Empfaengeradresse fehlt');
			return false;
		}
		if ( empty( $this->getFrom() ) )
		{
			// Error, stop mailing
			$this->setMailErrormessage('Absenderadresse fehlt');
			return false;
		}
		if ( empty( $this->getHeaders() ) )
		{
			// Error, stop mailing
			$this->setMailErrormessage('Mailheaders/Absenderadresse fehlt');
			return false;
		}
		if ( empty( $this->getSubject() ) )
		{
			// Error, stop mailing
			$this->setMailErrormessage('Betreff fehlt');
			return false;
		}
		if ( empty( $this->getMessage() ) )
		{
			// Error, stop mailing
			$this->setMailErrormessage('Nachricht fehlt');
			return false;
		}

		// Test
		//$this->setTo('');

		// -------------------------------------------------------------------
		// send mail
		// -------------------------------------------------------------------
		$success = mail($this->getTo(), $this->getSubject(), $this->getMessage(), $this->getHeaders());

		if ($success)
		{
			// -------------------------------------------------------------------
			// Success, lokaler Mailserver hat die Mail angenommen
			// -------------------------------------------------------------------
			return true;
		}
		else
		{
			// -------------------------------------------------------------------
			// Fehler, lokaler Mailserver hat die Mail abgelehnt
			// -------------------------------------------------------------------
			// Get last error message - only for windows.
			// https://stackoverflow.com/questions/3186725/how-can-i-get-the-error-message-for-the-mail-function/20203870#20203870
		    $lastErrorMessage = error_get_last()['message'];

			// Set Mail Errormessage
			$this->setMailErrormessage('Die E-Mail wurde nicht zum Versand angenommen');
			return false;
		}

	}


}
// eoClass
