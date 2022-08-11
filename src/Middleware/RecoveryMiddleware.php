<?php
declare(strict_types=1);

namespace App\Middleware;

use Authentication\AuthenticationServiceInterface;
use Cake\Core\Configure;
use Cake\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Recovery middleware
 */
class RecoveryMiddleware implements MiddlewareInterface
{
    /**
     * Process method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($this->check($request));
    }

    /**
     * If Recovery mode is on, check that user has 'admin' role.
     * On failure, throws UnauthenticatedException, return request otherwise.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @return \Psr\Http\Message\ServerRequestInterface
     * @throws \Authentication\Authenticator\UnauthenticatedException
     */
    private function check(ServerRequestInterface $request): ServerRequestInterface
    {
        if (!Configure::check('Recovery')) {
            return $request;
        }

        $service = $request->getAttribute('authentication');
        if (!$service instanceof AuthenticationServiceInterface) {
            return $request;
        }

        $user = $request->getAttribute('identity');
        if (empty($user) || in_array('admin', (array)$user->get('roles'))) {
            return $request;
        }

        $service->clearIdentity($request, new Response());
        /** @var \Cake\Http\ServerRequest $sr */
        $sr = $request;
        $ex = new \Authentication\Authenticator\UnauthenticatedException();
        $sr->getFlash()->setExceptionMessage($ex);

        throw $ex;
    }
}
