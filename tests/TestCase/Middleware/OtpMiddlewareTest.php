<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware;

use App\Middleware\OtpMiddleware;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequestFactory;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * {@see \App\Middleware\OtpMiddleware} Test Case
 *
 * @coversDefaultClass \App\Middleware\OtpMiddleware
 */
class OtpMiddlewareTest extends TestCase
{
    /**
     * Test process method.
     *
     * @return void
     * @covers ::process()
     */
    public function testProcess(): void
    {
        // empty conf
        $middleware = new OtpMiddleware();
        $handler = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new Response())->withLocation('/');
            }
        };
        $response = $middleware->process(ServerRequestFactory::fromGlobals(), $handler);
        static::assertEquals('/', $response->getHeaderLine('Location'));

        Configure::class::write('Otp', ['send' => '/otp']);

        // path OTP
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withUri($request->getUri()->withPath('/otp'));
        $response = $middleware->process($request, $handler);
        static::assertEquals('/', $response->getHeaderLine('Location'));

        // path non OTP
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withUri($request->getUri()->withPath('/test'));
        $response = $middleware->process($request, $handler);
        static::assertEquals('/', $response->getHeaderLine('Location'));

        // path no OTP but no pending OTP
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withUri($request->getUri()->withPath('/test'));
        $response = $middleware->process($request, $handler);
        static::assertEquals('/', $response->getHeaderLine('Location'));

        // path no OTP and pending OTP
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withUri($request->getUri()->withPath('/test'));
        $request->getSession()->write('Otp.pending', true);
        $response = $middleware->process($request, $handler);
        static::assertEquals('/otp', $response->getHeaderLine('Location'));
    }
}
