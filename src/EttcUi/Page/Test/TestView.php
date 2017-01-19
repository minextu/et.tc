<?php namespace Minextu\EttcUi\Page\Test;

use Minextu\EttcUi\Page\AbstractPageView;

class TestView extends AbstractPageView
{
    public function getTitle()
    {
        return "Test";
    }

    public function getHeading()
    {
        return "Test";
    }

    public function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/TestView.html");
    }
}
