<?php
namespace App\Test\Utils;

use App\Controller\ImportController;

/**
 * @uses \App\Controller\ImportController
 */
class ImportControllerSample extends ImportController
{
    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null): void
    {
        // do nothing
    }
}
