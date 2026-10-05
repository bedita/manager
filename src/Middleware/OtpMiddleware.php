<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Core\Configure;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Otp middleware
 */
class OtpMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $conf = (array)Configure::read('Otp');
        if (empty($conf)) {
            return $handler->handle($request);
        }
        $path = $request->getUri()->getPath();
        if (in_array($path, ['/otp', '/otp/verify'])) {
            return $handler->handle($request);
        }
        /** @var \Cake\Http\ServerRequest $request */
        if (!$request->getSession()->check('Otp.pending')) {
            return $handler->handle($request);
        }

        return (new Response())->withLocation('/otp');
    }
}
