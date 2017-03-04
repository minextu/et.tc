<?php namespace Minextu\EttcUi\Page\Settings;

use Minextu\EttcUi\Page\AbstractPageView;

class SettingsView extends AbstractPageView
{
    /**
     * All templates placeholder
     * @var   array
     */
    private $placeholders = [];

    private $selectedTab;

    public function getTitle()
    {
        return $this->presenter->getTabTitle();
    }

    public function getHeading()
    {
        return "Settings";
    }

    public function getSubHeading()
    {
        return $this->presenter->getTabTitle();
    }

    /**
     * Shows the content for the given tab
     * @param    string   $tab   Tab name
     */
    public function showTab($tab)
    {
        $this->selectedTab = $tab;

        $tabContent = $this->template->convertTemplate(__DIR__."/templates/tabs/$tab.html");
        $this->placeholders['MSG_TabContent'] = $tabContent;
    }

    public function setTabs($tabs)
    {
        $tabsHtml = "";

        foreach ($tabs as $tab => $title) {
            $placeholders = [
                "MSG_Title" => $title,
                "MSG_Link" => "__PATH_Root__/Settings/$tab"
            ];

            if ($tab == $this->selectedTab) {
                $tabsHtml .= $this->template->convertTemplate(__DIR__."/templates/NavEntrySelected.html", $placeholders);
            } else {
                $tabsHtml .= $this->template->convertTemplate(__DIR__."/templates/NavEntry.html", $placeholders);
            }
        }

        $this->placeholders["MSG_Tabs"] = $tabsHtml;
    }

    public function setApiKeys($keys)
    {
        $keysHtml = "";
        foreach ($keys as $key) {
            $placeholders = [
                "MSG_KeyId" => $key['id'],
                "MSG_KeyTitle" => $key['title'],
                "MSG_Key" => $key['key'],
                "MSG_KeyCreation" => $key['createDate'],
                "MSG_KeyLastUse" => $key['lastUseDate']
            ];
            $keysHtml .= $this->template->convertTemplate(__DIR__."/templates/tabs/ApiKeysEntry.html", $placeholders);
        }

        $this->placeholders['MSG_ApiKeyEntries'] = $keysHtml;
    }

    public function generateHtml()
    {
        return $this->template->convertTemplate(__DIR__."/templates/SettingsView.html", $this->placeholders);
    }
}
