<?php
namespace App\Test\TestCase\Controller;

use App\Core\Bulk\CustomBulkActionInterface;

class CustomBulkAction implements CustomBulkActionInterface
{
    /**
     * @inheritDoc
     */
    public function bulkAction(array $ids, string $type): array
    {
        return [];
    }
}
