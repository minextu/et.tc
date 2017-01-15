<?php namespace Minextu\EttcUi\Page\Start;

use Minextu\EttcUi\Page\AbstractPageView;

class StartView extends AbstractPageView
{
    public function getTitle()
    {
        return "Start";
    }

    public function getHeading()
    {
        return "Start";
    }

    public function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/StartView.html");
    }
}
