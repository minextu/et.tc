<?php namespace nexttrex\EttcUi\Page\Login;
use nexttrex\EttcUi\Page\AbstractPagePresenter;

class LoginPresenter extends AbstractPagePresenter
{
    function init()
    {

    }

    public function loginClicked($nick, $pw)
    {
        $loginStatus = $this->model->checkLogin($nick,$pw);
        if ($loginStatus)
            $this->view->redirectToStart();
        else
            $this->view->showError("Wrong e-mail/nickname or password");
    }
}
