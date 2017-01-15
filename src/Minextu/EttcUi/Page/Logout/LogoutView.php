<?php namespace Minextu\EttcUi\Page\Logout;

use Minextu\EttcUi\Page\AbstractPageView;

class LogoutView extends AbstractPageView
{
    public function getTitle()
    {
        return "Logout";
    }

    public function getHeading()
    {
        return "Logout";
    }

    public function generateHtml()
    {
        return false;
    }

    /**
     * Redirects the user to the starting page using a php header
     */
    public function redirectToStart()
    {
        header("Location: " . $this->path);
        die();
    }
}
