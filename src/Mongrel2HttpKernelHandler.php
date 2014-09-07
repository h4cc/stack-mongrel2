<?php

/*
 * This file is part of the h4cc/stack-mongrel2 package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\StackMongrel2;

use h4cc\Mongrel2\Handler;
use h4cc\Mongrel2\Request as MongrelRequest;
use h4cc\Mongrel2\Response as MongrelResponse;
use h4cc\Mongrel2\Transport;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * A handler for working with requests and responses from
 * a mongrel2 webserver and processing them with a HttpKernel.
 */
class Mongrel2HttpKernelHandler
{
    private $kernel;

    public function __construct(HttpKernelInterface $kernel, $pull, $pub)
    {
        $this->kernel = $kernel;
        $this->handler = new Handler(new Transport($pull, $pub));
    }

    /**
     * Main loop.
     */
    public function run()
    {
        // TODO: Exit should be possible here.
        while (true) {
            // TODO: Make this non-blocking and provide a idle callback.
            $request = $this->waitForNextRequest();
            $response = $this->kernel->handle($request);
            // TODO: Make this non-blocking maybe?
            $this->sendResponseToMongrel($request, $response);
        }
    }

    /**
     * A blocking wait for new requests.
     *
     * @return Request
     */
    private function waitForNextRequest()
    {
        $mongrelRequest = $this->handler->receiveRequest();

        $request = $this->createSymfony2Request($mongrelRequest);

        return $request;
    }

    /**
     * Sending back a response to mongrel2 webserver.
     *
     * @param Request $request
     * @param Response $response
     */
    private function sendResponseToMongrel(Request $request, Response $response)
    {
        $response->prepare($request);

        // Map back our Symfony Response to a MongrelResponse.
        $mongrelResponse = new MongrelResponse(
            $request->attributes->get('mongrel2_uuid'),
            [$request->attributes->get('mongrel2_listener')]
        );

        $mongrelResponse->setContent($response->getContent());

        $headers = $response->headers->all();
        foreach ($response->headers->getCookies() as $cookie) {
            $headers['Set-Cookie'][] = $cookie;
        }
        $mongrelResponse->setHeaders($headers);

        $mongrelResponse->setHttpVersion($response->getProtocolVersion());
        $mongrelResponse->setStatusCode($response->getStatusCode());
        $mongrelResponse->setReasonPhrase(Response::$statusTexts[$response->getStatusCode()]);

        $this->handler->sendResponse($mongrelResponse);
    }

    /**
     * Mapping a MongrelRequest to a SymfonyRequest.
     *
     * @param MongrelRequest $mongrelRequest
     * @return Request
     */
    private function createSymfony2Request(MongrelRequest $mongrelRequest)
    {
        // Create a Symfony request from a Mongrel request.
        $request = Request::create(
            $mongrelRequest->getPath(),
            $mongrelRequest->getMethod(),
            $mongrelRequest->getQuery(),
            $mongrelRequest->getCookies(),
            $mongrelRequest->getFiles(),
            $mongrelRequest->getServer(),
            $mongrelRequest->getBody()
        );

        // Need to replace headers.
        $request->headers->replace($mongrelRequest->getHeaders());
        $request->query->replace($mongrelRequest->getQuery());
        $request->request->replace($mongrelRequest->getPost());

        // Store needed values for response back to mongrel2.
        $request->attributes->set('mongrel2_uuid', $mongrelRequest->getUuid());
        $request->attributes->set('mongrel2_listener', $mongrelRequest->getListener());

        return $request;
    }
} 