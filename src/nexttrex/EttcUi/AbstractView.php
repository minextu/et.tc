<?php namespace nexttrex\EttcUi;

 /**
  * A Class extending this will render the user interface
  */
abstract class AbstractView
{
    /**
    * The external path to the folder containing the index.php
    * @var string
    */
    protected $path;

    /**
     * Template
     * @var   Template object
     */
    protected $template;

    /**
     * The Presenter for this View
     * @var   AbstractPresenter
     */
    protected $presenter;

    /**
     * @param   string   $path    The external path to the folder containing the index.php
     */
    final function __construct($path)
    {
        $this->path = $path;
        $this->template = new Template($this->path."/assets");
    }

    /**
     * @param   AbstractPresenter   $presenter   The Presenter for this View
     */
    final function setPresenter($presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * Returns html code for this view
     * @return   string   html code for this view
     */
    abstract function generateHtml();

    /**
     * Will be called after setView and setModel
     */
    function init()
    {

    }
}
