<?php

use Illuminate\Database\Capsule\Manager;

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
	    ]
    ],
]);

$container = $app->getContainer();

$dbManager = new Manager();
$dbManager->addConnection($container['settings']['database']);
$dbManager->setAsGlobal();
$dbManager->bootEloquent();

dump(\App\Models\Image::where('uuid', '550e8400-e29b-41d4-a716-446655440000')->first());
exit;
require_once __DIR__ . '/../routes/api.php';
