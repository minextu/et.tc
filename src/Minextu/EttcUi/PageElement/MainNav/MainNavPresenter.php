<?php namespace Minextu\EttcUi\PageElement\MainNav;
use Minextu\EttcUi\PageElement\AbstractPageElementPresenter;

class MainNavPresenter extends AbstractPageElementPresenter
{
    /**
     * Send all entries to the view
     */
    function init()
    {
        $entries = $this->model->getEntries();
        $this->view->setEntries($entries);
    }
}
