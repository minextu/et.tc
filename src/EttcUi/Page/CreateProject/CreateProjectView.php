<?php namespace Minextu\EttcUi\Page\CreateProject;

use Minextu\EttcUi\Page\AbstractPageView;

class CreateProjectView extends AbstractPageView
{
    /**
     * Contains possible warning, error or success message
     * @var   string
     */
    private $message;

    public function getTitle()
    {
        return "Create Project";
    }

    public function getHeading()
    {
        return "Create Project";
    }

    /**
     * Check if user has submited the create form and send values to presenter
     */
    public function init()
    {
        if (isset($_POST['create'])) {
            $title = isset($_POST['title']) ? $_POST['title'] : false;
            $description = isset($_POST['description']) ? $_POST['description'] : false;
            $this->presenter->addProjectClicked($title, $description);
        }
    }

    /**
     * Shows an Error message
     * @param    string   $text   Text of the message
     */
    public function showError($text)
    {
        $placeholders = [
            "MSG_text" => $text
        ];
        $this->message = $this->template->convertTemplate(__DIR__."/templates/CreateProjectError.html", $placeholders);
    }

    /**
     * Redirects the user to the projects page using a php header
     */
    public function redirectToProjects()
    {
        header("Location: " . $this->path . "/Projects");
        die();
    }

    public function generateHtml()
    {
        $placeholders = [
            "MSG_message" => $this->message
        ];

        return $this->template->convertTemplate(__DIR__."/templates/CreateProjectView.html", $placeholders);
    }
}
