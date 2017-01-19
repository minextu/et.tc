<?php namespace Minextu\EttcUi;

/**
 * An instance will render the webpage, by using the mvp concept
 */
class EttcUi
{
    /**
     * External path to the root directory of ettc
     * @var   String
     */
    private $rootDir;

    /**
     * main ettc object
     * @var   \Minextu\Ettc\Ettc
     */
    private $ettc;

    /**
     * The main presenter
     * @var   Main\MainPresenter
     */
    private $presenter;

    private $pageElements = ["MainNav", "UserNav"];

    /**
     * @param   \Minextu\Ettc\Ettc   $ettc       Main ettc object
     * @param   String   $rootDir    External path to the root directory
     * @param   String   $pageName   Page name to render
     */
    public function __construct($ettc, $rootDir, $pageName)
    {
        $this->rootDir = $rootDir;
        $this->ettc = $ettc;
        $this->start($pageName);
    }

    /**
     * Will init all classes
     * @param   String   $pageName   Page name to render
     */
    private function start($pageName)
    {
        $pageElementPresenters = $this->initPageElements();
        $pagePresenter = $this->initPage($pageName);
        $status = $this->init($pagePresenter, $pageElementPresenters, $pageName);

        if (!$status) {
            $this->start("Error404");
        }
    }

    /**
     * Generates the complete HTML code for this page
     * @return   string   Complete HTML code for this page
     */
    public function generateHtml()
    {
        return $this->presenter->getView()->generateHtml();
    }

    /**
     * Inits all model, view, presenter classes and links them together
     * @param    Page\AbstractPagePresenter                  $pagePresenter           The presenter of the page to be rendered
     * @param    PageElement\AbstractPageElementPresenter[]  $pageElementPresenters   All presenters in the PageElement folder
     * @param    String                                      $requestedpageName       Page name to render
     * @return   bool                                                                 False if the given page is unknown, True otherwise
     */
    private function init($pagePresenter, $pageElementPresenters, $requestedPageName)
    {
        $model = new Main\MainModel();
        $view = new Main\MainView($this->rootDir);
        $presenter = new Main\MainPresenter();

        // link vars for main
        $presenter->setView($view);
        $presenter->setModel($model);
        $presenter->setPageElementPresenters($pageElementPresenters);
        $presenter->setPagePresenter($pagePresenter);
        $view->setPresenter($presenter);

        // link models
        $model->setDb($this->ettc->getDb());
        $pagePresenter->getModel()->setMainModel($model);
        foreach ($pageElementPresenters as $elementPresenter) {
            $elementPresenter->getModel()->setMainModel($model);
        }

        // tell page presenter about any subpage
        $subPage = substr(strstr($requestedPageName, '/', false), 1);
        $continue = $pagePresenter->setSubPage($subPage);
        // return false if the presenter does not know this subpage
        if (!$continue) {
            return false;
        }

        // init all views
        $pagePresenter->getView()->init();
        foreach ($pageElementPresenters as $elementPresenter) {
            $elementPresenter->getView()->init();
        }
        $presenter->getView()->init();

        // init all models
        $pagePresenter->getModel()->init();
        foreach ($pageElementPresenters as $elementPresenter) {
            $elementPresenter->getModel()->init();
        }
        $presenter->getModel()->init();

        // init all presenters
        $pagePresenter->setMainPresenter($presenter);
        $pagePresenter->initPage();
        $pagePresenter->init();
        foreach ($pageElementPresenters as $elementPresenter) {
            $elementPresenter->init();
        }
        $presenter->init();

        $this->presenter = $presenter;

        return true;
    }

    private function initPageElements()
    {
        $presenters = [];
        foreach ($this->pageElements as $element) {
            // generate names for all classes
            $elementClassName =  "Minextu\\EttcUi\\PageElement\\$element\\$element";
            $elementModelName = $elementClassName . "Model";
            $elementViewName = $elementClassName . "View";
            $elementPresenterName = $elementClassName . "Presenter";

            // create instances
            $model = new $elementModelName();
            $view = new $elementViewName($this->rootDir);
            $presenter = new $elementPresenterName();

            // link vars
            $presenter->setView($view);
            $presenter->setModel($model);
            $view->setPresenter($presenter);

            $presenters[$element] = $presenter;
        }

        return $presenters;
    }

    /**
     * Inits the presenter of the page to be rendered by creating and linking the model and view classes
     * @param    string   $requestedPageName   Page to be rendered
     * @return   AbstractPresenter               Presenter class for the page
     */
    private function initPage($requestedPageName)
    {
        // only consider portion before the first / as page name
        $mainPage = strstr($requestedPageName, '/', true) ?: $requestedPageName;

        // get all available pages
        $availablePages = $this->getPages();

        // If the Page does not exist show a 404 Page
        $key = array_search($mainPage, $availablePages);
        if (!$key) {
            $pageName = "Error404";
        } else {
            $pageName = $availablePages[$key];
        }

        // generate names for all classes
        $pageClassName =  "Minextu\\EttcUi\\Page\\$pageName\\$pageName";
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
    * Generates an Array of available pages by scanning all Folders in src/Minextu/EttcUi/Page
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
