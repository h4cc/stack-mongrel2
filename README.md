# h4cc/stack-mongrel2

A adpater for using StackPHP with Mongrel2 via the HttpKernelInterface.

This allows using Applications like Silex or Symfony, to be run behind a Mongrel2 Webserver easily.

## Usage

Have a look at `web/index.php` for example usage.

## Current State

This package builds on `h4cc/mongrel2` and is limited to those mapping capabilities.


Currently, there is no population of expected values from `$_FILES`, so _no_ fileuploads.
Also the asynchronous file upload or websockets are not yet mapped.


Any help in providing these features is appreciated :)
