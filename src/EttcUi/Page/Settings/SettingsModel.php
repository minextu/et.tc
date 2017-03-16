<?php namespace Minextu\EttcUi\Page\Settings;

use \Minextu\EttcUi\Page\AbstractPageModel;
use \Minextu\EttcApi\ApiKey\ApiKeyList;

class SettingsModel extends AbstractPageModel
{
    private $availableTabs = ["Profile" => "Profile", "Password" => "Password", "ApiKeys" => "Api keys"];
    private $currentTab;

    /**
     * Sets the current Tab
     * @param   string   $tab   Current tab
     */
    public function setTab($tab)
    {
        // set default tab
        if (empty($tab)) {
            $tab = "Profile";
        }

        $this->currentTab = $tab;

        return key_exists($tab, $this->availableTabs);
    }

    public function getTab()
    {
        return $this->currentTab;
    }

    public function getTabTitle()
    {
        return $this->availableTabs[$this->currentTab];
    }

    public function getAvailableTabs()
    {
        return $this->availableTabs;
    }

    /**
     * Get all api keys for this user using EttcApi
     * @return   bool|array        All api keys as array on success, False otherwise
     */
    public function getApiKeys()
    {
        $apiKeyApi = new ApiKeyList($this->mainModel->getDb());
        $answer = $apiKeyApi->get();

        if (isset($answer['error'])) {
            return false;
        } else {
            return $answer['items'];
        }
    }
}
