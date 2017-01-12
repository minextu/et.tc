<?php namespace nexttrex\EttcUi\Page\Login;
use nexttrex\EttcUi\Page\AbstractPageView;

class LoginView extends AbstractPageView
{
    private $message;

    function getTitle()
    {
        return "Login";
    }

    function getHeading()
    {
        return "Login";
    }

    function generateHtml()
    {
        $placeholders = [
            "MSG_message" => $this->message
        ];
        return $this->template->convertTemplate(__DIR__."/templates/LoginView.html", $placeholders);
    }

    function init()
    {
        if (isset($_POST['login']))
        {
            $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            $this->presenter->loginClicked($nickname, $password);
        }
    }

    function showError($text)
    {
        $placeholders = [
            "MSG_text" => $text
        ];
        $this->message = $this->template->convertTemplate(__DIR__."/templates/LoginError.html", $placeholders);
    }

    function redirectToStart()
    {
        header("Location: " . $this->path);
        die();
    }
}
