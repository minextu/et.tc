<?php namespace Minextu\EttcUi\Page\Logout;

use Minextu\EttcUi\Page\AbstractPageView;

class LogoutView extends AbstractPageView
{
    /**
     * Contains possible warning, error or success message
     *
     * @var string
     */
    private $message;

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
        return $this->message;
    }

    /**
     * Shows an Error message
     *
     * @param string $text Text of the message
     */
    public function showError($text)
    {
        $placeholders = [
            "MSG_text" => $text
        ];
        $this->message = $this->template->convertTemplate(__DIR__."/templates/LogoutError.html", $placeholders);
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
