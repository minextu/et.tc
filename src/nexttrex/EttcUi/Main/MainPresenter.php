<?php namespace nexttrex\EttcUi\Main;
use nexttrex\EttcUi\AbstractPresenter;

class MainPresenter extends AbstractPresenter
{
    private $mainNavPresenter;
    private $pagePresenter;

    function setMainNavPresenter($mainNavPresenter)
    {
        $this->mainNavPresenter = $mainNavPresenter;
    }

    function setPagePresenter($pagePresenter)
    {
        $this->pagePresenter = $pagePresenter;
    }

    function init()
    {
        $mainNavHtml = $this->mainNavPresenter->getView()->generateHtml();
        $this->view->setMainNav($mainNavHtml);

        $pageHtml = $this->pagePresenter->getView()->generateHtml();
        $this->view->setPage($pageHtml);
    }

    function setTitle($title)
    {
        $this->view->setTitle($title);
    }

    function setHeading($heading)
    {
        $this->view->setHeading($heading);
    }

    function setSubHeading($subHeading)
    {
        $this->view->setSubHeading($subHeading);
    }
}
