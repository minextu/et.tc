<?php namespace Minextu\EttcUi\Page\Login;
use Minextu\EttcUi\Page\AbstractPagePresenter;

class LoginPresenter extends AbstractPagePresenter
{
    function init()
    {

    }

    /**
     * Checks if the given nickname and password are correct and sets the user status to be logged in
     * @param    string   $nick   User nickname
     * @param    string   $pw     User password
     */
    public function loginClicked($nick, $pw)
    {
        $check = $this->model->checkLogin($nick,$pw);
        if ($check)
        {
            $this->model->login($nick);
            $this->view->redirectToStart();
        }
        else
            $this->view->showError("Wrong e-mail/nickname or password");
    }
}
