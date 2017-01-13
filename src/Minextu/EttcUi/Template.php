<?php namespace Minextu\EttcUi;

/**
* An instance can generate proper HTML code out of a templates
*/
class Template
{
	/**
	* The external path to the assets folder
	* @var string
	*/
	private $path;
	/**
	* An Array of all HTML Templates
	* @var array
	*/
	private $templates;

	/**
	* Creates an instance
	*
	* @param  string  $path       The external path to the folder containing the index.php
	* @access public
	*/
	function __construct($path)
	{
		// set the external theme path
		$this->path = $path;
	}

	/**
	* Converts a HTML Template by replacing the Placeholders with the given values
	*
	* @param  string  $templateFile  Template File to be used
	* @param  array   $values        An array containing all values to be replaced. The index indicates the Name of a placeholder
	* @return string                 The converted Template as HTML Code
	*/
	public function convertTemplate($templateFile, $values=[])
	{
		if (!is_file($templateFile))
			throw new Exception("Template '$templateFile' could not be found.");

		$convertedTemplate = file_get_contents($templateFile);

		// replace all placeholders with their values
		foreach ($values as $placeholderName => $value)
		{
			$convertedTemplate = str_replace("__" . $placeholderName . "__", $value, $convertedTemplate);
		}

		return $convertedTemplate;
	}
}
