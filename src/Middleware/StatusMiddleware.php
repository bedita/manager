<?php
declare(strict_types=1);

namespace App\Middleware;

use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Exception\ServiceUnavailableException;
use Cake\Log\LogTrait;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Middleware to serve a status endpoint.
 */
class StatusMiddleware implements MiddlewareInterface
{
    use LogTrait;

    /**
     * Path to the status endpoint.
     *
     * @var string
     */
    protected string $path = '/status';

    /**
     * Status middleware constructor.
     *
     * @param string|null $path Path to the status endpoint.
     */
    public function __construct(?string $path = null)
    {
        $this->path = $path ?? $this->path;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() !== $this->path) {
            // Request is not addressed to the status endpoint.
            return $handler->handle($request);
        }

        if (!in_array(strtoupper($request->getMethod()), ['GET', 'HEAD', 'OPTIONS'])) {
            // Status endpoint only supports GET and HEAD requests.
            throw new MethodNotAllowedException();
        }

        $queryParams = $request->getQueryParams();
        $full = filter_var($queryParams['full'] ?? null, FILTER_VALIDATE_BOOLEAN);
        if (!$full) {
            // Just a liveliness probe to check whether the server is up.
            return new EmptyResponse();
        }

        try {
            // Check if the API is reachable and up.
            $client = ApiClientProvider::getApiClient();
            $client->get('/status'); // Upon error, this will throw a \BEdita\SDK\BEditaClientException.
        } catch (Throwable $e) {
            $this->log(sprintf('API status endpoint failed or unreachable: %s', $e), LogLevel::CRITICAL);

            throw new ServiceUnavailableException('API status returned an error or is unreachable');
        }

        return new EmptyResponse();
    }
}
