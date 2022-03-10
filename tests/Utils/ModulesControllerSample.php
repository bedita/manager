<?php
namespace App\Test\Utils;

use App\Controller\ModulesController;
use BEdita\SDK\BEditaClient;

/**
 * Sample controller wrapper, to add useful methods for test
 *
 * @uses \App\Controller\ModulesController
 */
class ModulesControllerSample extends ModulesController
{
    /**
     * Getter for objectType protected var
     *
     * @return string
     */
    public function getObjectType(): string
    {
        return $this->objectType;
    }

    /**
     * Public version of parent function (protected) descendants
     *
     * @return void
     */
    public function descendants(): array
    {
        return parent::descendants();
    }

    /**
     * Public version of parent function (protected)
     *
     * @return void
     */
    public function availableRelationshipsUrl(string $relation): string
    {
        return parent::availableRelationshipsUrl($relation);
    }

    /**
     * Update media urls, public for testing
     *
     * @param array $response The response
     * @return void
     */
    public function updateMediaUrls(array &$response): void
    {
        parent::updateMediaUrls($response);
    }

    /**
     * Create new object from ajax request.
     *
     * @return void
     */
    public function getApiClient(): BEditaClient
    {
        return $this->apiClient;
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
