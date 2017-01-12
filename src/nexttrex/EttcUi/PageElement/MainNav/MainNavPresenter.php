<?php namespace nexttrex\EttcUi\PageElement\MainNav;
use nexttrex\EttcUi\PageElement\AbstractPageElementPresenter;

class MainNavPresenter extends AbstractPageElementPresenter
{
    function init()
    {
        $entries = $this->model->getEntries();
        $this->view->setEntries($entries);
    }
}
