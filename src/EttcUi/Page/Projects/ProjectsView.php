<?php namespace Minextu\EttcUi\Page\Projects;

use Minextu\EttcUi\Page\AbstractPageView;

class ProjectsView extends AbstractPageView
{
    public function getTitle()
    {
        return "Projects";
    }

    public function getHeading()
    {
        return "Projects";
    }

    public function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/ProjectsView.html");
    }
}
