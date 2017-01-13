<?php namespace Minextu\EttcUi\Main;
use Minextu\EttcUi\AbstractPresenter;

class MainPresenter extends AbstractPresenter
{
    /**
     * Contains all presenters in the PageElement folder
     * @var   \Minextu\EttcUi\PageElement\AbstractPageElementPresenter[]
     */
    private $pageElementPresenters;
    /**
     * Presenter for the current page
     * @var   \Minextu\EttcUi\Page\AbstractPagePresenter
     */
    private $pagePresenter;

    /**
     * @param   \Minextu\EttcUi\PageElement\AbstractPageElementPresenter[]  $pageElementPresenters  All presenters in the PageElement folder
     */
    function setPageElementPresenters($pageElementPresenters)
    {
        $this->pageElementPresenters = $pageElementPresenters;
    }

    /**
     * @param   \Minextu\EttcUi\Page\AbstractPagePresenter   $pagePresenter   Presenter for the current page
     */
    function setPagePresenter($pagePresenter)
    {
        $this->pagePresenter = $pagePresenter;
    }

    /**
     * Sends all views to MainView
     */
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

    /**
     * Sends page title to view
     * @param   string   $title   Title of this Page
     */
    function setTitle($title)
    {
        $this->view->setTitle($title);
    }

    /**
     * Sends page heading to view
     * @param   string   $heading   Heading of this Page
     */
    function setHeading($heading)
    {
        $this->view->setHeading($heading);
    }

    /**
     * Sends page sub heading to view
     * @param   string   $subHeading   Sub heading of this Page
     */
    function setSubHeading($subHeading)
    {
        $this->view->setSubHeading($subHeading);
    }
}
