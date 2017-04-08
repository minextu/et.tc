<?php namespace Minextu\EttcUi\PageElement\MainNav;

use Minextu\EttcUi\PageElement\AbstractPageElementView;

class MainNavView extends AbstractPageElementView
{
    /**
     * All entries in the main navigation. Keys contain the text, values contain the url
     *
     * @var array
     */
    private $entries;

    /**
     * @param   array $entries All entries in the main navigation. Keys contain the text, values contain the url
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }

    public function generateHtml()
    {
        $entries = $this->generateEntriesHtml();

        $placeholders = array(
            "NAV_Entries" => $entries
        );
        $html = $this->template->convertTemplate(__DIR__."/templates/MainNavView.html", $placeholders);

        return $html;
    }

    /**
     * Generates the html code for all navigation entries
     *
     * @return string   Html code for all entries
     */
    private function generateEntriesHtml()
    {
        if (!isset($this->entries)) {
            throw new Exception("No entries were set.");
        }

        $entriesHtml = "";
        foreach ($this->entries as $name => $link) {
            $placeholders = array(
                'MSG_EntryName' => $name,
                'ENTRY_Url' => $link
            );

            $entriesHtml .= $this->template->convertTemplate(__DIR__."/templates/MainNavViewEntry.html", $placeholders);
        }

        return $entriesHtml;
    }
}
