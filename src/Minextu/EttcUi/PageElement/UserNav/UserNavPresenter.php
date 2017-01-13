<?php namespace Minextu\EttcUi\PageElement\UserNav;
use Minextu\EttcUi\PageElement\AbstractPageElementPresenter;

class UserNavPresenter extends AbstractPageElementPresenter
{
    /**
     * Checks if the user is logged in and tells the view about it
     */
    function init()
    {
        $loggedIn = $this->model->checkLogin();
        $this->view->setLoggedIn($loggedIn);
    }

    /**
     * Gets the nickname by using the model
     * @return   string|bool  Nickname of the user if logged in, False otherwise
     */
    function getNickname()
    {
        return $this->model->getNickname();
    }

    /**
     * Gets the avatar image for the user
     * @return   string   Avatar image url for this user
     */
    function getAvatar()
    {
        return $this->model->getAvatar();
    }
}
