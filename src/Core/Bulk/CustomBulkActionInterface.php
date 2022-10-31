<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Core\Bulk;

/**
 * Custom bulk action interface
 */
interface CustomBulkActionInterface
{
    /**
     * Perform bulk action.
     *
     * @param array $ids Object IDs
     * @param string $type Object type
     * @return array Error details on failure, empty array on success
     */
    public function bulkAction(array $ids, string $type): array;
}
