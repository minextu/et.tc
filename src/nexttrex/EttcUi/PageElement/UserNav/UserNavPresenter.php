<?php namespace nexttrex\EttcUi\PageElement\UserNav;
use nexttrex\EttcUi\PageElement\AbstractPageElementPresenter;

class UserNavPresenter extends AbstractPageElementPresenter
{
    function init()
    {
        $loggedIn = $this->model->checkLogin();
        $this->view->setLoggedIn($loggedIn);
    }

    function getNickname()
    {
        return $this->model->getNickname();
    }

    function getAvatar()
    {
        return $this->model->getAvatar();
    }
}
