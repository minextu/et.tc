<?php namespace Minextu\EttcUi;

/**
 * A Class extending this will communicate between the model and the view classes
 */
abstract class AbstractPresenter
{
    /**
     * The view for this presenter
     *
     * @var AbstractView
     */
    protected $view;
    /**
     * The model for this presenter
     *
     * @var ModelInterface
     */
    protected $model;

    /**
     * @return   AbstractView   View for this presenter
     */
    final public function getView()
    {
        return $this->view;
    }

    /**
     * @param   AbstractView $view View for this presenter
     */
    final public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return   AbstractModel   Model for this presenter
     */
    final public function getModel()
    {
        return $this->model;
    }

    /**
     * @param   ModelInterface $model Model for this presenter
     */
    final public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Will be called after setView and setModel
     */
    public function init()
    {
    }
}
