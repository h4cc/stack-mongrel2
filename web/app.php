<?php

/*
 * This file is part of the h4cc/stack-mongrel2 package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This file contains a demo silex app.

require_once(__DIR__.'/../vendor/autoload.php');

$app = new Silex\Application();

$app['debug'] = true;

// Hello world handler with name parameter.
$app->get('/', function (\Symfony\Component\HttpFoundation\Request $request) {
    $name = $request->get('name', 'world');
    return new \Symfony\Component\HttpFoundation\Response('Hello '.$name);
});

// Simple POST form.
$app->match('/form', function (\Symfony\Component\HttpFoundation\Request $request) {
    return new \Symfony\Component\HttpFoundation\Response('
    <html>
        <body>
            <p>Method: '.var_export($request->getMethod(), true).'</p>
            <p>POST values: '.var_export($request->request->all(), true).'</p>
            <form method="POST">
                <input type="text" name="foo" value="bar" />
                <input type="submit" value="Send" />
            </form>
        </body>
    </html>
    ');
});

// Simple file upload.
$app->match('/fileupload', function (\Symfony\Component\HttpFoundation\Request $request) {

    /** @var  $file \Symfony\Component\HttpFoundation\File\UploadedFile */
    $file = $request->files->get('aFile');
    $fileContent = '';
    if($file) {
        $fileContent = file_get_contents($file->getPathname());
    }

    return new \Symfony\Component\HttpFoundation\Response('
    <html>
        <body>
            <p>Method: '.var_export($request->getMethod(), true).'</p>
            <p>POST values: '.var_export($request->request->all(), true).'</p>
            <p>FILES values: '.var_export($request->files->all(), true).'</p>
            <p>$_FILES values: '.var_export($_FILES, true).'</p>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="aFile"/>
                <input type="submit" value="Upload" />
            </form>
            <p>File content "aFile": '.$fileContent.'</pre>
        </body>
    </html>
    ');
});

// Set and get Cookie.
$app->get('/cookie', function (\Symfony\Component\HttpFoundation\Request $request) {
    $uuid = $request->cookies->get('last_uuid');
    $response = new \Symfony\Component\HttpFoundation\Response('Your last Mongrel Request-UUID: '.$uuid);
    $response->headers->setCookie(
        new \Symfony\Component\HttpFoundation\Cookie(
            'last_uuid', $request->attributes->get('mongrel2_uuid').'_'.$request->attributes->get('mongrel2_listener')
        )
    );
    return $response;
});

return $app;