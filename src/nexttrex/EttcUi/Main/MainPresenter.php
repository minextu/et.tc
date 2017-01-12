<?php namespace nexttrex\EttcUi\Main;
use nexttrex\EttcUi\AbstractPresenter;

class MainPresenter extends AbstractPresenter
{
    private $pageElementPresenters;
    private $pagePresenter;

    function setPageElementPresenters($pageElementPresenters)
    {
        $this->pageElementPresenters = $pageElementPresenters;
    }

    function setPagePresenter($pagePresenter)
    {
        $this->pagePresenter = $pagePresenter;
    }

    function init()
    {
        $mainNav = $this->pageElementPresenters['MainNav']->getView();
        $this->view->setMainNav($mainNav);

        $userNav = $this->pageElementPresenters['UserNav']->getView();
        $this->view->setUserNav($userNav);

        $page = $this->pagePresenter->getView();
        $this->view->setPage($page);
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
