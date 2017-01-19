<?php namespace Minextu\EttcUi\Page\Logout;

use Minextu\EttcUi\Page\AbstractPagePresenter;
use Minextu\EttcUi\Exception;

class LogoutPresenter extends AbstractPagePresenter
{
    /**
     * Logouts the user and redirects to the starting page
     */
    public function init()
    {
        try {
            $this->model->logout();
            $this->view->redirectToStart();
        } catch (Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }
}
