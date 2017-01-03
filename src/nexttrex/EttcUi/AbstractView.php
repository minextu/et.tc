<?php namespace nexttrex\EttcUi;

abstract class AbstractView
{
    /**
    * The external path to the folder containing the index.php
    * @var string
    */
    protected $path;

    protected $template;
    protected $presenter;

    final function __construct($path)
    {
        $this->path = $path;
        $this->template = new Template($this->path."/assets");
    }

    final function setPresenter($presenter)
    {
        $this->presenter = $presenter;
    }

    abstract function generateHtml();
}
