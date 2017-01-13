<?php namespace Minextu\EttcUi\Page\Login;
use Minextu\EttcUi\Page\AbstractPageView;

class LoginView extends AbstractPageView
{
    /**
     * Contains possible warning, error or success message
     * @var   [type]
     */
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

    /**
     * Checks if the login form was submited and passes the values to the presenter
     */
    function init()
    {
        if (isset($_POST['login']))
        {
            $nickname = isset($_POST['nickname']) ? $_POST['nickname'] : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            $this->presenter->loginClicked($nickname, $password);
        }
    }

    /**
     * Shows an Error message
     * @param    string   $text   Text of the message
     */
    function showError($text)
    {
        $placeholders = [
            "MSG_text" => $text
        ];
        $this->message = $this->template->convertTemplate(__DIR__."/templates/LoginError.html", $placeholders);
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
