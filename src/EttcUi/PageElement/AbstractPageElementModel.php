<?php namespace Minextu\EttcUi\PageElement;

use Minextu\EttcUi\AbstractModel;

abstract class AbstractPageElementModel extends AbstractModel
{
    /**
     * The main model
     *
     * @var \Minextu\EttcUi\Main\MainModel
     */
    protected $mainModel;

    /**
     * @param   \Minextu\EttcUi\Main\MainModel $mainModel The main model
     */
    final public function setMainModel($mainModel)
    {
        $this->mainModel = $mainModel;
    }
}
