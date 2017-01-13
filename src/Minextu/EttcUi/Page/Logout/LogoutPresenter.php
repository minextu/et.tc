<?php namespace Minextu\EttcUi\Page\Logout;
use Minextu\EttcUi\Page\AbstractPagePresenter;

class LogoutPresenter extends AbstractPagePresenter
{
    /**
     * Logouts the user and redirects to the starting page
     */
    function init()
    {
        $this->model->logout();
        $this->view->redirectToStart();
    }
}
