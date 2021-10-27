<?php
namespace App\Controller\Component;

use App\Utility\AccessControl;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Component;
use Cake\Utility\Hash;

/**
 * Api component
 */
class ApiComponent extends Component
{
    /**
     * Call api get objects by object type and query
     *
     * @param string $objectType The object type
     * @param array $query The query
     * @return array
     */
    public function getObjects(string $objectType, array $query): array
    {
        $modulesComponent = $this->getController()->Modules;
        $moduleName = (string)$modulesComponent->getConfig('currentModuleName');
        $user = (array)$this->getController()->Auth->user();
        $roles = (array)Hash::get($user, 'roles');
        $query = AccessControl::filteredQuery($moduleName, $roles) + $query;
        $classMethod = AccessControl::filteredClassMethod($objectType, $roles);
        $class = empty($classMethod['class']) ? ApiClientProvider::getApiClient() : $classMethod['class']();
        $method = empty($classMethod['method']) ? 'getObjects' : $classMethod['method'];

        return (array)$class->$method($objectType, $query);
    }
}
