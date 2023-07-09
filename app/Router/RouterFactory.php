<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
        $router = new RouteList;
        $router[] = $module = new RouteList('Admin');
        $module->addRoute('admin/<presenter>/<action>', 'Dashboard:default');

        $router[] = $module = new RouteList('Eshop');
        $module->addRoute('<presenter>/<action>', 'Homepage:default');
        $module->addRoute('item/<id>', 'Item:detail');
        $module->addRoute('<url>', 'Item:default');
        return $router;
	}
}
