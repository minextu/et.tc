<?php namespace Minextu\EttcApi;

use Respect\Rest\Router;
use Minextu\Ettc\Ettc;

class EttcApi
{
    private static $rootDir;
    private static $router;
    private static $db;

    public static function run($rootDir)
    {
        self::init($rootDir);
        $router = self::$router;

        self::init404();
        self::setRoutes();
        self::encode();
    }

    private static function setRoutes()
    {
        $r = self::$router;

        // Project
        $r->get('/v1/projects', new Projects);
        $r->post('/v1/project/create', new Project\Create);
        $r->delete('/v1/project/delete/*', new Project\Delete);

        // User
        $r->post('/v1/user/login', new User\Login);
        $r->post('/v1/user/logout', new User\Logout);
    }

    private static function init404()
    {
        // Show custom 404 Message
        // TODO: Find a better way than catch all
        self::$router->any('/**', function () {
            header("HTTP/1.0 404 Not Found");
            return 'Not found!';
        });
    }

    private static function init($rootDir)
    {
        self::$rootDir = $rootDir;
        self::$router = new Router($rootDir);
    }

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
