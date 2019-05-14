<?php

class MyFormfield
{

	// -------------------------------------------------------------------
	// Properties of a form field
	// -------------------------------------------------------------------
	private $type, $id, $css, $style, $name, $value, $label, $html, $choices;
	private $required;
	private $errorMessages = array();


	// -------------------------------------------------------------------
	// Common methods
	// -------------------------------------------------------------------
	public function __construct($options)
	{
		if (is_array($options))
		{

			$this->setType(   $this->getOption('type', $options) );
			$this->setId(     $this->getOption('id', $options) );
			$this->setCss(    $this->getOption('css', $options) );
			$this->setStyle(  $this->getOption('style', $options) );

			$this->setValue(    $this->getOption('value', $options) );
			$this->setName(     $this->getOption('name', $options) );
			$this->setLabel(    $this->getOption('label', $options) );
			$this->setRequired( $this->getOption('required', $options) );
			$this->setChoices(  $this->getOption('choices', $options) );

		}

		// -------------------------------------------------------------------
		// Get user input and validate
		// -------------------------------------------------------------------
		// Only if form was submitted
		// -------------------------------------------------------------------
		// Why? Without this check, the form would be shown with validator
		// messages, on the first time.
		// -------------------------------------------------------------------
		if (is_array($_POST) && count($_POST)) {

			// Add $_POST value for this field
			$this->getUserinput();

			// Validate this field
			$this->validateUserinput();

		}

	}



	// -------------------------------------------------------------------
	// Validate user input
	// -------------------------------------------------------------------
    public function validateUserinput()
    {

		if ($this->getRequired() === true)
		{
			// This field is required

			if ( empty($this->getValue() ))
			{
				// REQUIRED
				// A required field is empty
				// Add error message to this field
				$message = array();
				$message['type'] = 'required';
				$message['message'] = 'Bitte ausf&uuml;llen';
				$this->addErrorMessage($message);
			}

			// else
			// {
			// 	// FILLED IN
			// 	// A required field is empty
			// 	// Add error message to this field
			// 	$message = array();
			// 	$message['type'] = 'required';
			// 	$message['message'] = 'Feld ist ausgefuuml;llt';
			// 	$this->addErrorMessage($message);
			//
			// }
		}

		// else
		// {
		// 	// NOT REQUIRED
		// 	$message = array();
		// 	$message['type'] = 'required';
		// 	$message['message'] = 'This field is not required';
		// 	$this->addErrorMessage($message);
		//
		// }



    }

    public function addErrorMessage($message)
    {
		$this->errorMessages[] = $message;

    }


    public function getErrormessages()
    {
		return $this->errorMessages;

    }

    public function getNumberOfErrormessages()
    {
		return count($this->errorMessages);

    }



    public function getErrormessagesAsHtml()
    {

		// -------------------------------------------------------------------
		// Loop collection
		// -------------------------------------------------------------------
		$html = '';
		foreach ($this->errorMessages as $record) {

			$html = '<div class="myformfieldValidationerror">'. $record['message'] .'</div>';

		} //eoForeach

		return $html;

    }




	// -------------------------------------------------------------------
	// Populate field values from $_POST
	// -------------------------------------------------------------------
    public function getUserinput()
    {
		$fieldname = $this->getName();

		if (array_key_exists($fieldname, $_POST))
		{

			// Clean the user input
			$userinput = $this->cleanUserinput( $_POST[$fieldname] );

			// Set field value
			$this->setValue( $userinput );

		}

    }


    public function cleanUserinput($userinput)
    {
	  	$userinput = trim($userinput);
  		$userinput = stripslashes($userinput);
  		$userinput = htmlspecialchars($userinput);

		return ($userinput);
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
	// type
	// -------------------------------------------------------------------
    public function setType( $value )
    {
		$this->type = strtolower($value);
    }

    public function getType()
    {
		return $this->type;
    }


	// -------------------------------------------------------------------
	// id
	// -------------------------------------------------------------------
    public function setId( $value )
    {
		$this->id = $value;
    }

    public function getId()
    {
		return $this->id;
    }


	// -------------------------------------------------------------------
	// css
	// -------------------------------------------------------------------
    public function setCss( $value )
    {
		$this->css = $value;
    }

    public function getCss()
    {
		return $this->css;
    }


	// -------------------------------------------------------------------
	// style
	// -------------------------------------------------------------------
    public function setStyle( $value )
    {
		$this->style = $value;
    }

    public function getStyle()
    {
		return $this->style;
    }


	// -------------------------------------------------------------------
	// name
	// -------------------------------------------------------------------
    public function setName( $value )
    {
		$this->name = $value;
    }

    public function getName()
    {
		return $this->name;
    }


	// -------------------------------------------------------------------
	// value
	// -------------------------------------------------------------------
    public function setValue( $value )
    {
		$this->value = $value;
    }

    public function getValue()
    {
		return $this->value;
    }


	// -------------------------------------------------------------------
	// label
	// -------------------------------------------------------------------
    public function setLabel( $value )
    {
		$this->label = $value;
    }

    public function getLabel()
    {
		return $this->label;
    }


    public function getLabelWithAsteriskIfRequired()
    {

		$asterisk = '';
		if ($this->getRequired()) {
			$asterisk = '*';
		}
		return $this->label . $asterisk;
    }


	// -------------------------------------------------------------------
	// html
	// -------------------------------------------------------------------
    public function setHtml( $value )
    {
		$this->html = $value;
    }

    public function getHtml()
    {
		return $this->html;
    }

	// -------------------------------------------------------------------
	// required
	// -------------------------------------------------------------------
    public function setRequired( $value )
    {
		$this->required = $value;
    }

    public function getRequired()
    {
		return $this->required;
    }


	// -------------------------------------------------------------------
	// choices (for radio, checkbox, select)
	// -------------------------------------------------------------------
    public function setChoices( $value )
    {
		$this->choices = $value;
    }

    public function getChoices()
    {
		return $this->choices;
    }




	// -------------------------------------------------------------------
	// generate html for the form field
	// -------------------------------------------------------------------
    public function generateFormfieldHtml()
    {

		$html = '';
	    switch ($this->getType()) {
	      case 'text':
		  	$html = $this->getTextHtml();
	      break;
	      case 'radio':
		  	$html = $this->getRadioHtml();
	      break;
	      case 'submit':
		  	$html = $this->getSubmitHtml();
	      break;
	      default:
	          // Unknown field type
	    }

		return $html;
    }

    public function getTextHtml()
    {
		// Render a text field
		// <input type="text" id="anrede" name="anrede" >
		$html = '<input '. $this->renderSharedAttributes1() .' '. $this->renderSharedAttributes2() .'>';

		return $html;
	}

    public function getSubmitHtml()
    {
		// Render a text field
		// <input type="text" id="anrede" name="anrede" >
		$html = '<input '. $this->renderSharedAttributes1() .' '. $this->renderSharedAttributes2() .'>';

		return $html;
	}

    public function getRadioHtml()
    {

		// -------------------------------------------------------------------
		// HTML
		// -------------------------------------------------------------------
		// <input type="radio" name="gender" value="female" checked>Female</input>
		// <input type="radio" name="gender" value="male"          >Male</input>

		// -------------------------------------------------------------------
		// Loop collection
		// -------------------------------------------------------------------
		$html = '';
		foreach ($this->getChoices() as $record)
		{
			if ($this->getValue() === $record['value'])
			{
				$checked = 'checked';
			}
			else
			{
				$checked = '';
			}

			$html .= '<input '. $this->renderSharedAttributes1() .' value="'. $record['value'] .'" '. $checked .'>'. $record['name'];
			$html .= PHP_EOL;

		} //eoForeach

		return $html;
	}


    public function renderSharedAttributes1()
    {
		// type, name, id, class, style
		$html = '';

		if ($this->getType())
		{
			$html .= 'type="'. $this->getType(). '"';
			$html .= ' '; // Abstand zum naechsten Attribut
		}
		if ($this->getId())
		{
			$html .= 'id="'. $this->getId(). '"';
			$html .= ' '; // Abstand zum naechsten Attribut
		}
		if ($this->getCss())
		{
			$html .= 'css="'. $this->getCss(). '"';
			$html .= ' '; // Abstand zum naechsten Attribut
		}
		if ($this->getStyle())
		{
			$html .= 'style="'. $this->getStyle(). '"';
			$html .= ' '; // Abstand zum naechsten Attribut
		}
		if ($this->getName())
		{
			$html .= 'name="'. $this->getName(). '"';
			$html .= ' '; // Abstand zum naechsten Attribut
		}

		return $html;


    }

    public function renderSharedAttributes2()
    {
		// value
		$html = '';

		if ($this->getValue())
		{
			$html .= 'value="'. $this->getValue(). '"';
			$html .= ' '; // Abstand zum naechsten Attribut
		}

		return $html;


    }


}
//eoClass
