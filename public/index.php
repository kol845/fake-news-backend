<?php
declare(strict_types=1);

use App\Api; #Import the Api class from App(src folder)

require __DIR__ . '/../vendor/autoload.php';

(new Api())->run(); # create new instance of Api and run run()
