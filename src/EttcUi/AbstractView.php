<?php namespace Minextu\EttcUi;

/**
  * A Class extending this will render the user interface
  */
abstract class AbstractView
{
    /**
    * The external path to the folder containing the index.php
     *
    * @var string
    */
    protected $path;

    /**
     * Template
     *
     * @var Template object
     */
    protected $template;

    /**
     * The Presenter for this View
     *
     * @var AbstractPresenter
     */
    protected $presenter;

    /**
     * @param   string $path The external path to the folder containing the index.php
     */
    final public function __construct($path)
    {
        $this->path = $path;
        $this->template = new Template($this->path."/assets");
    }

    /**
     * @param   AbstractPresenter $presenter The Presenter for this View
     */
    final public function setPresenter($presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * Returns html code for this view
     *
     * @return string   html code for this view
     */
    abstract public function generateHtml();

    /**
     * Will be called after setView and setModel
     */
    public function init()
    {
    }
}
