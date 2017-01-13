<?php namespace nexttrex\EttcUi\Page\Logout;
use nexttrex\EttcUi\Page\AbstractPagePresenter;

class LogoutPresenter extends AbstractPagePresenter
{
    function init()
    {
        $this->model->logout();
        $this->view->redirectToStart();
    }
}
