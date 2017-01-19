<?php namespace Minextu\EttcUi\Page\Project;

use Minextu\EttcUi\Page\AbstractPageView;

class ProjectView extends AbstractPageView
{
    /**
     * All templates placeholder
     * @var   array
     */
    private $placeholders = [];

    public function getTitle()
    {
        return $this->presenter->getProjectTitle();
    }

    public function getHeading()
    {
        return $this->presenter->getProjectTitle();
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
        $this->placeholders['MSG_ProjectDescription'] = $description;
    }

    public function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/ProjectView.html", $this->placeholders);
    }
}
