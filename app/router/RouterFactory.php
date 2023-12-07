<?php

namespace App\Router;

use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;
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
        $router[] = new Route('index.php', 'Front:Home:default', iRouter::ONE_WAY);

        $router[] = $adminRouter = new RouteList('Admin');
        $adminRouter[] = new Route('admin/<presenter>/<action>[/<id>]', 'Homepage:default');

        // $router[] = $clientRouter = new RouteList('Klient');
        // $clientRouter[] = new Route('klient/<presenter>/<action>[/<id>]', 'Homepage:default');

        $router[] = $frontRouter = new RouteList('Cron');
        $frontRouter[] = new Route('cron/<presenter>/<action>[/<id>]', 'Homepage:default');

        $router[] = $frontRouter = new RouteList('Front');
        $frontRouter[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');

        return $router;
    }

}
