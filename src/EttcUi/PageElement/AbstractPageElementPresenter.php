<?php namespace Minextu\EttcUi\PageElement;

use Minextu\EttcUi\AbstractPresenter;

abstract class AbstractPageElementPresenter extends AbstractPresenter
{
    /**
     * The main presenter
     *
     * @var \Minextu\EttcUi\Main\MainPresenter
     */
    protected $mainPresenter;

    /**
     * @param   \Minextu\EttcUi\Main\MainPresenter $mainPresenter The main presenter
     */
    final public function setMainPresenter($mainPresenter)
    {
        $this->mainPresenter = $mainPresenter;
    }
}
