<?php

namespace App\Router;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;
use Nette\StaticClass;

/**
 * Router factory.
 */
final class RouterFactory
{
    use StaticClass;

    /**
     * @return RouteList
     */
    public static function createRouter(): RouteList
    {
        $router = new RouteList;

        // Route::$defaultFlags = IRouter::SECURED;
        // TODO: could be fine to have it as parameter instead of hard code SECURED/INSECURED
        $router->addRoute('index.php', 'Front:Home:default', Router::ONE_WAY);

        $router->withModule('Admin')
            ->addRoute('admin/<presenter>/<action>[/<id>]', 'Homepage:default');

        // $router[] = $clientRouter = new RouteList('Klient');
        // $clientRouter[] = new Route('klient/<presenter>/<action>[/<id>]', 'Homepage:default');

        $router->withModule('Cron')
            ->addRoute('cron/<presenter>/<action>[/<id>]', 'Homepage:default');

        $router->withModule('Front')
            ->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');

        return $router;
    }

}
