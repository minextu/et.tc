<?php namespace Minextu\EttcUi\Page\Logout;
use Minextu\EttcUi\Page\AbstractPageView;

class LogoutView extends AbstractPageView
{
    function getTitle()
    {
        return "Logout";
    }

    function getHeading()
    {
        return "Logout";
    }

    function generateHtml()
    {
        return false;
    }

    /**
     * Redirects the user to the starting page using a php header
     */
    function redirectToStart()
    {
        header("Location: " . $this->path);
        die();
    }
}
