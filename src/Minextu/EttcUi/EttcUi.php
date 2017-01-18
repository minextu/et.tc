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
     * Will init all classed and display the given page
     * @param   \Minextu\Ettc\Ettc   $ettc       Main ettc object
     * @param   String   $rootDir    External path to the root directory
     * @param   String   $pageName   Page name to render
     */
    public function __construct($ettc, $rootDir, $pageName)
    {
        $this->rootDir = $rootDir;
        $this->ettc = $ettc;

        $pageElementPresenters = $this->initPageElements();
        $pagePresenter = $this->initPage($pageName);
        $this->init($pagePresenter, $pageElementPresenters);
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
     * @return   Main\MainPresenter                                                   The main presenter
     */
    private function init($pagePresenter, $pageElementPresenters)
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

        // init models
        $model->setEttc($this->ettc);
        $pagePresenter->getModel()->setMainModel($model);
        foreach ($pageElementPresenters as $elementPresenter) {
            $elementPresenter->getModel()->setMainModel($model);
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
     * @param    string   $pageName   Page to be rendered
     * @return   AbstractPresenter               Presenter class for the page
     */
    private function initPage($pageName)
    {
        // get all available pages
        $availablePages = $this->getPages();

        // If the Page does not exist show a 404 Page
        if (!in_array($pageName, $availablePages)) {
            $pageName = "Error404";
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
