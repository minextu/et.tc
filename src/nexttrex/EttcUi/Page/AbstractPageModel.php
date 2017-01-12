<?php namespace nexttrex\EttcUi\Page;
use nexttrex\EttcUi\AbstractModel;

abstract class AbstractPageModel extends AbstractModel
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
