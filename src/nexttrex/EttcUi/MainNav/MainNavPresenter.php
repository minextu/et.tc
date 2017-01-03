<?php namespace nexttrex\EttcUi\mainNav;
use nexttrex\ettcUi\AbstractPresenter;

class MainNavPresenter extends AbstractPresenter
{
    function init()
    {
        $entries = $this->model->getEntries();
        $this->view->setEntries($entries);
    }
}
