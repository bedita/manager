<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Middleware;

use App\Application;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Project middleware.
 *
 * Multi projects support (optional): detect current `_project` name in session and try to load matching config file from `config/projects` folder.
 * After that app plugins are loaded via configuration.
 */
class ProjectMiddleware implements MiddlewareInterface
{
    /**
     * Application instance
     *
     * @var \App\Application
     */
    protected $Application;

    /**
     * Projects config base path
     *
     * @var string
     */
    protected $projectsConfigPath = CONFIG . 'projects' . DS;

    /**
     * Constructor
     *
     * @param \App\Application $app The application instance.
     * @param string|null $configPath Projects config path.
     */
    public function __construct(Application $app, ?string $configPath = null)
    {
        $this->Application = $app;
        if (!empty($configPath)) {
            $this->projectsConfigPath = $configPath;
        }
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \Cake\Http\ServerRequest $request */
        $project = $this->detectProject($request);
        Application::loadProjectConfig((string)$project, $this->projectsConfigPath);
        $this->Application->loadPluginsFromConfig();

        return $handler->handle($request);
    }

    /**
     * Detect project in use from session or request, if any.
     * On empty session or request, or missing project name, `null` is returned.
     *
     * @param \Cake\Http\ServerRequest $request The request.
     * @return string|null
     */
    protected function detectProject(ServerRequest $request): ?string
    {
        $session = $request->getSession();
        if ($session->check('_project')) {
            return (string)$session->read('_project');
        }

        $project = $request->getData('project');
        if (!empty($project)) {
            return $project;
        }

        return null;
    }
}
