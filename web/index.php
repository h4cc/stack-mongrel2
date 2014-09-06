<?php

/*
 * This file is part of the h4cc/stack-mongrel2 package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Some demo code for using this handler.
 */

require_once(__DIR__.'/../vendor/autoload.php');

class MyKernel implements \Symfony\Component\HttpKernel\HttpKernelInterface
{
    public function handle(\Symfony\Component\HttpFoundation\Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        // Creating some output, that shows all provided data by mongrel2.

        $response = var_export($request->server->all(), true);
        $response .= var_export($request->query->all(), true);
        $response .= var_export($request->files->all(), true);
        $response .= var_export($request->cookies->all(), true);

        return new \Symfony\Component\HttpFoundation\Response('<pre>'.$response.'</pre');
    }
}

$kernel = new MyKernel();

$client = new \h4cc\StackMongrel2\Mongrel2HttpKernelHandler($kernel, 'tcp://127.0.0.1:9997', 'tcp://127.0.0.1:9996');
$client->run();
