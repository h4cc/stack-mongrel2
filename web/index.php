<?php

/*
 * This file is part of the h4cc/stack-mongrel2 package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


// This file can be used via apache or PHP internal webserver.

$app = require_once(__DIR__.'/app.php');

$app->run();
