<?php
namespace App\Test\Utils;

use App\Controller\Component\ModulesComponent;
use App\Controller\Component\SchemaComponent;
use App\Controller\ModulesController;
use BEdita\SDK\BEditaClient;

/**
 * Sample controller wrapper, to add useful methods for test
 *
 * @uses \App\Controller\ModulesController
 */
class ModulesControllerSample extends ModulesController
{
    public ?BEditaClient $apiClient;
    public ModulesComponent $Modules;
    public SchemaComponent $Schema;

    /**
     * Public version of parent function (protected) descendants
     *
     * @return array
     */
    public function descendants(): array
    {
        return $this->Schema->descendants($this->objectType);
    }

    /**
     * Public version of parent function (protected)
     *
     * @return string
     */
    public function availableRelationshipsUrl(string $relation): string
    {
        return parent::availableRelationshipsUrl($relation);
    }

    /**
     * Create new object from ajax request.
     *
     * @return void
     */
    public function save(): void
    {
        parent::save();
    }
}
