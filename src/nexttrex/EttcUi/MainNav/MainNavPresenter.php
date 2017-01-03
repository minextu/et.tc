<?php namespace nexttrex\ettcUi\mainNav;
use nexttrex\ettcUi\AbstractPresenter;

class MainNavPresenter extends AbstractPresenter
{
    function init()
    {
        $entries = $this->model->getEntries();
        $this->view->setEntries($entries);
    }
}
