<?php namespace Minextu\EttcUi\Page\Login;

use Minextu\EttcUi\Page\AbstractPagePresenter;
use Minextu\EttcUi\Exception;

class LoginPresenter extends AbstractPagePresenter
{
    public function init()
    {
    }

    /**
     * Checks if the given nickname and password are correct and sets the user status to be logged in
     * @param    string   $nick   User nickname
     * @param    string   $pw     User password
     */
    public function loginClicked($nick, $pw)
    {
        $check = $this->model->checkLogin($nick, $pw);
        if ($check) {
            try {
                $this->model->login($nick, $pw);
                $this->view->redirectToStart();
            } catch (Exception $e) {
                $this->view->showError($e->getMessage());
            }
        } else {
            $this->view->showError("Wrong e-mail/nickname or password");
        }
    }
}
