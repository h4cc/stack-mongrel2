# h4cc/stack-mongrel2

A adpater for using StackPHP with Mongrel2 via the HttpKernelInterface.

This allows using Applications like Silex or Symfony, to be run behind a Mongrel2 Webserver easily.

## Usage

Have a look at `web/handler.php` for example usage.

## Current State

This package builds on `h4cc/mongrel2` and is limited to those mapping capabilities.


Currently, there is a simple population values from `$_FILES` via `multipart/*` uploads.
Also the asynchronous file upload or websockets are not yet mapped.


Any help in providing these features is appreciated :)
