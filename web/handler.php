<?php

/*
 * This file is part of the h4cc/stack-mongrel2 package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This file contains a Mongrel2 Handler using the silex app.

$app = require_once(__DIR__.'/app.php');

$client = new \h4cc\StackMongrel2\Mongrel2HttpKernelHandler($app, 'tcp://127.0.0.1:9997', 'tcp://127.0.0.1:9996');
$client->run();
