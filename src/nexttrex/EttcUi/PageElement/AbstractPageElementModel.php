<?php namespace nexttrex\EttcUi\PageElement;
use nexttrex\EttcUi\AbstractModel;

abstract class AbstractPageElementModel extends AbstractModel
{
    /**
     * The main model
     * @var   \nexttrex\EttcUi\Main\MainModel
     */
    protected $mainModel;

    /**
     * @param   \nexttrex\EttcUi\Main\MainModel   $mainModel   The main model
     */
    final function setMainModel($mainModel)
    {
        $this->mainModel = $mainModel;
    }
}
