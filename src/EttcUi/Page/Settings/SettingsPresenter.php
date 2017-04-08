<?php namespace Minextu\EttcUi\Page\Settings;

use Minextu\EttcUi\Page\AbstractPagePresenter;

class SettingsPresenter extends AbstractPagePresenter
{
    /**
     * Check and set settings page
     *
     * @param  string $subPage The subpage to access for this page (setting tab in this case)
     * @return bool                True if the subpage is valid, False otherwise
     */
    public function setSubPage($subPage)
    {
        $tabExists = $this->model->setTab($subPage);
        return $tabExists;
    }

    /**
     * Gets all tabs from model and tell view about them
     */
    public function init()
    {
        $tab = $this->model->getTab();
        $this->view->showTab($tab);

        $tabs = $this->model->getAvailableTabs();
        $this->view->setTabs($tabs);

        if ($tab == "ApiKeys") {
            $apiKeys = $this->model->getApiKeys();
            $this->view->setApiKeys($apiKeys);
        }
    }

    public function getTabTitle()
    {
        return $this->model->getTabTitle();
    }
}
