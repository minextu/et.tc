<?php namespace nexttrex\EttcUi\Page;
use nexttrex\EttcUi\AbstractPresenter;

abstract class AbstractPagePresenter extends AbstractPresenter
{
    protected $mainPresenter;

    final function initPage()
    {
        $title = $this->model->getTitle();
        $heading = $this->model->getHeading();
        $subHeading = $this->model->getSubHeading();

        $this->mainPresenter->setTitle($title);
        $this->mainPresenter->setHeading($heading);
        $this->mainPresenter->setSubHeading($subHeading);
    }

    final function setMainPresenter($mainPresenter)
    {
        $this->mainPresenter = $mainPresenter;
    }
}
