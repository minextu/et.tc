<?php namespace nexttrex\EttcUi\MainNav;
use nexttrex\ettcUi\AbstractView;

class MainNavView extends AbstractView
{
    private $entries;

    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

    function generateHtml()
    {
        $entries = $this->generateEntriesHtml();

        $placeholders = array(
            "NAV_Entries" => $entries
        );
        $html = $this->template->convertTemplate(__DIR__."/templates/MainNavView.html", $placeholders);

        return $html;
    }

    private function generateEntriesHtml()
    {
        if (!isset($this->entries))
            throw new Exception("No entries were set.");

        $entriesHtml = "";
        foreach ($this->entries as $name => $link)
        {
            $placeholders = array(
                'MSG_EntryName' => $name,
                'ENTRY_Url' => $link
            );

            $entriesHtml .= $this->template->convertTemplate(__DIR__."/templates/MainNavViewEntry.html", $placeholders);
        }

        return $entriesHtml;
    }
}
