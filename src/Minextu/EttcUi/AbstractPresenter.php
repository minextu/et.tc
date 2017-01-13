<?php namespace Minextu\EttcUi;

/**
 * A Class extending this will communicate between the model and the view classes
 */
abstract class AbstractPresenter
{
    /**
     * The view for this presenter
     * @var   AbstractView
     */
    protected $view;
    /**
     * The model for this presenter
     * @var   ModelInterface
     */
    protected $model;

    /**
     * @return   AbstractView   View for this presenter
     */
    final function getView()
    {
        return $this->view;
    }

    /**
     * @param   AbstractView   $view   View for this presenter
     */
    final function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return   AbstractModel   Model for this presenter
     */
    final function getModel()
    {
        return $this->model;
    }

    /**
     * @param   ModelInterface   $model  Model for this presenter
     */
    final function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Will be called after setView and setModel
     */
    function init()
    {

    }
}
