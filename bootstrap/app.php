<?php

use Illuminate\Database\Capsule\Manager;
use Illuminate\Events\Dispatcher;
use Intervention\Image\ImageManager;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$app = new Slim\App([
    'settings' => [
        'displayErrorDetails' => getenv('APP_DEBUG') === 'true',

        'app' => [
            'name' => getenv('APP_NAME')
        ],

	    'database' => [
	    	'driver' => 'pgsql',
		    'host' => 'localhost',
		    'port' => 5432,
		    'database' => getenv('DB_NAME'),
		    'username' => getenv('DB_USER'),
		    'password' => getenv('DB_PWD'),
		    'charset' => 'utf8',
		    'collation' => 'utf8_unicode_ci',
		    'prefix' => '',
	    ],

	    'image' => [
	    	'cache' => [
	    		'path' => base_path('storage/cache/image')
		    ]
	    ]
    ],
]);

$container = $app->getContainer();
$container['image'] = function ($container) {
	$manager = new ImageManager();
	$manager->configure($container['settings']['image']);

	return $manager;
};

$dbManager = new Manager();
$dbManager->addConnection($container['settings']['database']);

$dbManager->setEventDispatcher(new Dispatcher());

$dbManager->setAsGlobal();
$dbManager->bootEloquent();

require_once __DIR__ . '/../routes/api.php';
