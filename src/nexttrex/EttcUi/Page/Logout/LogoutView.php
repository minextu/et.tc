<?php namespace nexttrex\EttcUi\Page\Logout;
use nexttrex\EttcUi\Page\AbstractPageView;

class LogoutView extends AbstractPageView
{
    private $message;

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

    function redirectToStart()
    {
        header("Location: " . $this->path);
        die();
    }
}
