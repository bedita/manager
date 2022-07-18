<?php
declare(strict_types=1);

namespace App\Middleware;

use Authentication\Authenticator\Result;
use Cake\Utility\Hash;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware to handle OAuth2 flow.
 */
class OAuth2Middleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $request->getAttribute('authenticationResult');
        if (empty($result) || !$result instanceof Result || !is_array($result->getData())) {
            return $handler->handle($request);
        }

        $authUrl = Hash::get($result->getData(), 'authUrl');
        if (empty($authUrl)) {
            return $handler->handle($request);
        }

        return new RedirectResponse($authUrl);
    }
}
