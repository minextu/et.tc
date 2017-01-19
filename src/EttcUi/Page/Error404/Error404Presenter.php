<?php namespace Minextu\EttcUi\Page\Error404;

use Minextu\EttcUi\Page\AbstractPagePresenter;

class Error404Presenter extends AbstractPagePresenter
{
    /**
     * Allow any subpage for a 404 error
     * @param   bool   $subpage   Always True
     */
    public function setSubPage($subpage)
    {
        return true;
    }
    
    public function init()
    {
    }
}
