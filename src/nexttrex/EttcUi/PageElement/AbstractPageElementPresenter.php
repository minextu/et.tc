<?php namespace nexttrex\EttcUi\PageElement;
use nexttrex\EttcUi\AbstractPresenter;

abstract class AbstractPageElementPresenter extends AbstractPresenter
{
    /**
     * The main presenter
     * @var   \nexttrex\EttcUi\Main\MainPresenter
     */
    protected $mainPresenter;

    /**
     * @param   \nexttrex\EttcUi\Main\MainPresenter   $mainPresenter   The main presenter
     */
    final function setMainPresenter($mainPresenter)
    {
        $this->mainPresenter = $mainPresenter;
    }
}
