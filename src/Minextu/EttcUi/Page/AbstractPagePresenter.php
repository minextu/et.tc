<?php namespace Minextu\EttcUi\Page;
use Minextu\EttcUi\AbstractPresenter;

abstract class AbstractPagePresenter extends AbstractPresenter
{
    /**
     * The main presenter
     * @var   \Minextu\EttcUi\Main\MainPresenter
     */
    protected $mainPresenter;

    /**
     * Get title, heading and other information out of the view for this page and send it to the main presenter
     */
    final function initPage()
    {
        $title = $this->view->getTitle();
        $heading = $this->view->getHeading();
        $subHeading = $this->view->getSubHeading();

        $this->mainPresenter->setTitle($title);
        $this->mainPresenter->setHeading($heading);
        $this->mainPresenter->setSubHeading($subHeading);
    }

    /**
     * @param   \Minextu\EttcUi\Main\MainPresenter   $mainPresenter   The main presenter
     */
    final function setMainPresenter($mainPresenter)
    {
        $this->mainPresenter = $mainPresenter;
    }
}
