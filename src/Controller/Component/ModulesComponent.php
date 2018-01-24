<?php
namespace App\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\Component;

/**
 * Component to load available modules.
 */
class ModulesComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'apiClient' => null,
        'currentModuleName' => null,
    ];

    /**
     * Read modules and project info from `/home' endpoint.
     *
     * @return void
     */
    public function beforeRender()
    {
        $modules = $this->getModules();
        $project = $this->getProject();

        $currentModuleName = $this->getConfig('currentModuleName');
        if (isset($currentModuleName)) {
            $currentModule = $this->getModuleByName($modules, $currentModuleName);
        }

        $this->getController()->set(compact('currentModule', 'modules', 'project'));
    }

    /**
     * Getter for home endpoint metadata.
     *
     * @return array
     */
    protected function getMeta()
    {
        $home = Cache::remember(
            sprintf('home_%d', $this->_registry->get('Auth')->user('id')),
            function () {
                return $this->getConfig('apiClient')->get('/home');
            }
        );

        return $home['meta'];
    }

    /**
     * Get list of available modules.
     *
     * @return array
     */
    public function getModules()
    {
        static $excludedModules = ['auth', 'admin', 'model', 'roles', 'signup', 'status', 'trash'];

        $meta = $this->getMeta();
        $modules = collection($meta['resources'])
            ->map(function (array $data, $endpoint) {
                $name = substr($endpoint, 1);

                return $data + compact('name');
            })
            ->reject(function (array $data) use ($excludedModules) {
                return in_array($data['name'], $excludedModules);
            })
            ->toList();

        return $modules;
    }

    /**
     * Get a module by its name.
     *
     * @param array $modules List of all modules.
     * @param string $name Name of module to extract.
     * @return array|null
     */
    public function getModuleByName(array $modules, $name)
    {
        foreach ($modules as $module) {
            if ($module['name'] === $name) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Get information about current project.
     *
     * @return array
     */
    public function getProject()
    {
        $meta = $this->getMeta();
        $project = [
            'name' => $meta['project']['name'],
            'version' => $meta['version'],
            'colophon' => '', // TODO: populate this value.
        ];

        return $project;
    }
}
