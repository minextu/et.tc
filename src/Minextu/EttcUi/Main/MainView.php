<?php namespace Minextu\EttcUi\Main;
use Minextu\EttcUi\AbstractView;

class MainView extends AbstractView
{
    /**
     * Contains all views in the PageElement folder + the current Page view
     * @var   \Minextu\EttcUi\AbstractView[]
     */
    private $pageElements;
    /**
     * Page title
     * @var   string
     */
    private $title;
    /**
     * Page heading
     * @var   string
     */
    private $heading;
    /**
     * Page sub heading
     * @var   string
     */
    private $subHeading;

    /**
     * @param   \Minextu\EttcUi\AbstractView[]   $pageElements   All views in the PageElement folder + the current Page view
     */
    function setPageElements($pageElements)
    {
        $this->pageElements = $pageElements;
    }

    /**
     * @param   string   $title   Page title
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param   string   $heading  Page heading
     */
    function setHeading($heading)
    {
        $this->heading = $heading;
    }

    /**
     * @param   string   $subHeading  Page sub heading
     */
    function setSubHeading($subHeading)
    {
        $this->subHeading = $subHeading;
    }

    function generateHtml()
    {
        // replace all views placeholders
        $placeholders = [];
        foreach ($this->pageElements as $name => $element)
        {
            $placeholders["VIEW_$name"] = $element->generateHtml();
        }

        // replace other generic placeholders
        $placeholders = array_merge($placeholders, array(
            'MSG_PageTitle' => $this->title,
            'MSG_PageHeading' => $this->heading,
            'MSG_PageSubHeading' => $this->subHeading,
            'PATH_Assets' => $this->path."/assets",
            'PATH_Root' => $this->path,
            'MSG_CurrentYear' => "2016 - " . date("Y")
        ));

        $html = $this->template->convertTemplate(__DIR__."/templates/MainView.html", $placeholders);
        return $html;
    }
}
