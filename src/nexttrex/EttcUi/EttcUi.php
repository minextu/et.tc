<?php namespace nexttrex\EttcUi;

class EttcUi
{
    private $rootDir;
    private $presenter;
    function __construct($rootDir, $pageName)
    {
        $this->rootDir = $rootDir;
        $pagePresenter = $this->initPage($pageName);
        $this->init($pagePresenter);
    }

    public function generateHtml()
    {
        return $this->presenter->getView()->generateHtml();
    }

    private function init($pagePresenter)
    {
        $model = new Main\MainModel();
        $mainNavModel = new MainNav\MainNavModel();

        $view = new Main\MainView($this->rootDir);
        $mainNavView = new MainNav\MainNavView($this->rootDir);

        $presenter = new Main\MainPresenter();
        $mainNavPresenter = new MainNav\MainNavPresenter();

        // link vars for main
        $presenter->setView($view);
        $presenter->setModel($model);
        $presenter->setMainNavPresenter($mainNavPresenter);
        $presenter->setPagePresenter($pagePresenter);
        $view->setPresenter($presenter);

        // link vars for mainNav
        $mainNavPresenter->setView($mainNavView);
        $mainNavPresenter->setModel($mainNavModel);
        $mainNavView->setPresenter($mainNavPresenter);

        // init all presenters
        $pagePresenter->setMainPresenter($presenter);
        $pagePresenter->initPage();
        $pagePresenter->init();
        $mainNavPresenter->init();
        $presenter->init();

        $this->presenter = $presenter;
    }

    private function initPage($pageName)
    {
        // get all available pages
        $availablePages = $this->getPages();

        // If the Page does not exist show a 404 Page
    	if (!in_array($pageName, $availablePages))
    		$pageName = "Error404";

        // generate names for all classes
		$pageClassName =  "nexttrex\\EttcUi\\Page\\$pageName\\$pageName";
        $pageModelName = $pageClassName . "Model";
        $pageViewName = $pageClassName . "View";
        $pagePresenterName = $pageClassName . "Presenter";

        // create instances
		$model = new $pageModelName();
        $view = new $pageViewName($this->rootDir);
        $presenter = new $pagePresenterName();

        // link vars
        $presenter->setView($view);
        $presenter->setModel($model);
        $view->setPresenter($presenter);

        return $presenter;
    }

    /**
    * Generates an Array of available pages by scanning all Folders in src/nexttrex/EttcUi/Page
    * @return  array  Contains all available pages
    */
    private function getPages()
    {
        $directory = __DIR__.'/Page/';
        // remove .. and . folder in unix
        $pageFolders = array_diff(scandir($directory), array('..', '.'));

        return $pageFolders;
    }
}
