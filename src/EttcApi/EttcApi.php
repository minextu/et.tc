<?php namespace Minextu\EttcApi;

use Respect\Rest\Router;
use Minextu\Ettc\Ettc;

/**
 * Handles routes to the api by using objects extending AbstractRoutable
 */
class EttcApi
{
    /**
     * Path to api.php in root folder
     * @var   string
     */
    private static $rootDir;
    /**
     * The main router object
     * @var   Respect\Rest\Router
     */
    private static $router;

    /**
     * Run the api
     * @param    string   $rootDir   Path to api.php in root folder
     * @param    Minextu\Ettc\Database\DatabaseInterface   $db        Database to be used
     */
    public static function run($rootDir, $db)
    {
        self::init($rootDir);

        self::init404();
        self::setRoutes($db);
        self::encode();
    }

    /**
     * Connect api calls with their objects
     * @param   Minextu\Ettc\Database\DatabaseInterface   $db   Database to be used
     */
    private static function setRoutes($db)
    {
        $r = self::$router;

        // ApiKey
        $r->post('/v1/apiKey/create', new ApiKey\Create($db));
        $r->post('/v1/apiKey/delete/*', new ApiKey\Delete($db));
        $r->get('/v1/apiKeys', new ApiKey\ApiKeyList($db));

        // Project
        $r->get('/v1/projects', new Project\ProjectList($db));
        $r->get('/v1/project/*', new Project\Project($db));
        $r->get('/v1/project/changelog/*', new Project\Changelog($db));
        $r->post('/v1/project/create', new Project\Create($db));
        $r->post('/v1/project/update/*', new Project\Update($db));
        $r->post('/v1/project/initGit/*', new Project\InitGit($db));
        $r->post('/v1/project/delete/*', new Project\Delete($db));

        // User
        $r->post('/v1/user/login', new User\Login($db));
        $r->post('/v1/user/logout', new User\Logout($db));
    }

    /**
     * Init the 404 message for unknown api calls
     */
    private static function init404()
    {
        // Show custom 404 Message
        // TODO: Find a better way than catch all
        self::$router->any('/**', function () {
            header("HTTP/1.0 404 Not Found");
            return ['error' => 'ApiNotFound'];
        });
    }

    /**
     * Init the main router object
     * @param    string   $rootDir   Path to api.php in root folder
     */
    private static function init($rootDir)
    {
        self::$rootDir = $rootDir;
        self::$router = new Router($rootDir);
    }

    /**
     * Encode the api answer in json or html, depending on the accept header
     */
    private static function encode()
    {
        $router = self::$router;

        // encode to json by default, use html as fallback (in browsers)
        $router->always('Accept', array(
            'application/json' => 'json_encode',
            'text/html' => function ($answer) {
                return self::htmlEncode($answer);
            }
        ));
    }

    /**
     * Use the answer array to generate html output for debug
     * @param    array   $answer   Api answer
     * @return   string             Html code for the api answer
     */
    private static function htmlEncode($answer)
    {
        \ref::config('expLvl', -1);
        \ref::config('showResourceInfo', false);
        \ref::config('showMethods', false);

        $prettyAnswer = @r($answer);
        // remove filename and line number
        $prettyAnswer = preg_replace('/<r data-backtrace.*?<\/r>/', "", $prettyAnswer);

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <title>et.tc API</title>
        </head>
        <body>
            <h1>et.tc API Answer</h1>
            " . $prettyAnswer . "
        </body>
        </html>
        ";
        return $html;
    }
}
