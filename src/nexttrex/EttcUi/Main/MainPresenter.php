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
        $pageElementViews = [];
        foreach ($this->pageElementPresenters as $name => $elementPresenter)
        {
            $pageElementViews[$name] = $elementPresenter->getView();
        }
        $pageElementViews['Page'] = $this->pagePresenter->getView();

        $this->view->setPageElements($pageElementViews);
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
