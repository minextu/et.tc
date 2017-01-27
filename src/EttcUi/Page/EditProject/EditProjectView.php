<?php namespace Minextu\EttcUi\Page\EditProject;

use Minextu\EttcUi\Page\AbstractPageView;

class EditProjectView extends AbstractPageView
{
    /**
     * All templates placeholder
     * @var   array
     */
    private $placeholders = [];

    /**
     * Contains possible warning, error or success message
     * @var   string
     */
    private $message;

    public function getTitle()
    {
        return "Edit " . $this->presenter->getProjectTitle();
    }

    public function getHeading()
    {
        return "Edit " . $this->presenter->getProjectTitle();
    }

    /**
     * Check if user has submited the create form and send values to presenter
     */
    public function init()
    {
        if (isset($_POST['update'])) {
            $this->presenter->updateProjectClicked();
        }
    }

    /**
     * Save project id to placeholder array
     * @param   int   $id   Project id
     */
    public function setId($id)
    {
        $this->placeholders['MSG_ProjectId'] = $id;
    }

    /**
     * Save project title to placeholder array
     * @param   string   $title   Project title
     */
    public function setTitle($title)
    {
        $this->placeholders['MSG_ProjectTitle'] = htmlspecialchars($title, ENT_QUOTES);
    }

    /**
     * Save project image to placeholder array
     * @param   string   $image   Image url
     */
    public function setImage($image)
    {
        $this->placeholders['MSG_ProjectImage'] = $image;
    }

    /**
     * Save project creation date to placeholder array
     * @param   string   $created  Date of creation
     */
    public function setCreateDate($created)
    {
        $this->placeholders['MSG_ProjectCreateDate'] = $created;
    }

    /**
     * Save project update date to placeholder array
     * @param   string   $update  Date of last update
     */
    public function setUpdateDate($updated)
    {
        $this->placeholders['MSG_ProjectUpdateDate'] = $updated;
    }

    /**
     * Save project description to placeholder array
     * @param   string   $description  Project description
     */
    public function setDescription($description)
    {
        $this->placeholders['MSG_ProjectDescription'] = htmlspecialchars($description, ENT_QUOTES);
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
        $this->message = $this->template->convertTemplate(__DIR__."/templates/EditProjectError.html", $placeholders);
    }

    /**
     * Redirects the user to the projects page using a php header
     */
    public function redirectToProject($id)
    {
        header("Location: " . $this->path . "/Project/$id");
        die();
    }

    public function generateHtml()
    {
        $this->placeholders['MSG_message'] = $this->message;
        return $this->template->convertTemplate(__DIR__."/templates/EditProjectView.html", $this->placeholders);
    }
}
