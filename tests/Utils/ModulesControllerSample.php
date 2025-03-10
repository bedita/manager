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
     * Get api client.
     *
     * @return \BEdita\SDK\BEditaClient
     */
    public function getApiClient(): BEditaClient
    {
        return $this->apiClient;
    }

    /**
     * Set api client.
     *
     * @param \BEdita\SDK\BEditaClient|null
     * @return void
     */
    public function setApiClient(?BEditaClient $client = null): void
    {
        $this->apiClient = $client;
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
