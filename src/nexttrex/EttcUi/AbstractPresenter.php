<?php namespace nexttrex\EttcUi;

abstract class AbstractPresenter
{
    protected $view;
    protected $model;

    final function getView()
    {
        return $this->view;
    }

    final function setView($view)
    {
        $this->view = $view;
    }

    final function setModel($model)
    {
        $this->model = $model;
    }

    function init()
    {

    }
}
