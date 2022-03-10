<?php
namespace App\Test\Utils;

use App\Controller\Model\ModelBaseController;

/**
 * @uses \App\Controller\Model\ModelBaseController
 */
class ModelController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'object_types';

    /**
     * Set resource type
     *
     * @param string $type Resource type
     * @return void
     */
    public function setResourceType(string $type): void
    {
        $this->resourceType = $type;
    }

    /**
     * Set single view
     *
     * @param bool $view Single view flag
     * @return void
     */
    public function setSingleView(bool $view): void
    {
        $this->singleView = $view;
    }
}
