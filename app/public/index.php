<?php

use App\Kernel;

if (file_exists(dirname(__DIR__) . '/c3.php')) {
    require_once dirname(__DIR__) . '/c3.php';
}

//if ($_SERVER['APP_ENV'] === 'test' && file_exists(dirname(__DIR__) . '/c3.php')) {
//    require_once dirname(__DIR__) . '/c3.php';
//}

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool)$context['APP_DEBUG']);
};
