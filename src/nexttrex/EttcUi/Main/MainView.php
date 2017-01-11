<?php namespace nexttrex\EttcUi\Main;
use nexttrex\EttcUi\AbstractView;

class MainView extends AbstractView
{
    private $mainNav;
    private $page;
    private $title;
    private $heading;
    private $subHeading;

    function setMainNav($mainNav)
    {
        $this->mainNav = $mainNav;
    }

    function setPage($page)
    {
        $this->page = $page;
    }

    function setTitle($title)
    {
        $this->title = $title;
    }

    function setHeading($heading)
    {
        $this->heading = $heading;
    }

    function setSubHeading($subHeading)
    {
        $this->subHeading = $subHeading;
    }

    function generateHtml()
    {
        $placeholders = array(
            'VIEW_MainNav' => $this->mainNav,
            'VIEW_Page' => $this->page,
            'MSG_PageTitle' => $this->title,
            'MSG_PageHeading' => $this->heading,
            'MSG_PageSubHeading' => $this->subHeading,
            'PATH_Assets' => $this->path."/assets",
            'PATH_Root' => $this->path,
            'MSG_CurrentYear' => "2016 - " . date("Y")
        );
        $html = $this->template->convertTemplate(__DIR__."/templates/MainView.html", $placeholders);

        return $html;
    }
}
