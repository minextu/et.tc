<?php namespace Minextu\EttcUi\Page;

use Minextu\EttcUi\AbstractPresenter;

abstract class AbstractPagePresenter extends AbstractPresenter
{
    /**
     * The main presenter
     *
     * @var \Minextu\EttcUi\Main\MainPresenter
     */
    protected $mainPresenter;

    /**
     * Get title, heading and other information out of the view for this page and send it to the main presenter
     */
    final public function initPage()
    {
        $title = $this->view->getTitle();
        $heading = $this->view->getHeading();
        $subHeading = $this->view->getSubHeading();

        $this->mainPresenter->setTitle($title);
        $this->mainPresenter->setHeading($heading);
        $this->mainPresenter->setSubHeading($subHeading);
    }

    /**
     * @param   \Minextu\EttcUi\Main\MainPresenter $mainPresenter The main presenter
     */
    final public function setMainPresenter($mainPresenter)
    {
        $this->mainPresenter = $mainPresenter;
    }

    /**
    * Set the subpage to given value. Will not allow any sub page by default and cause a 404
     *
    * @param  String $subpage the sub page including all slashes
    * @return bool              True if this sub page is valid, False otherwise
    */
    public function setSubPage($subpage)
    {
        if (empty($subpage)) {
            return true;
        } else {
            return false;
        }
    }
}
