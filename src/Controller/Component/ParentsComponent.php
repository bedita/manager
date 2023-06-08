<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Controller\Component;

use App\Utility\ApiClientTrait;
use Cake\Controller\Component;

/**
 * Parents component.
 * This component is used to add/remove parents to a folder.
 */
class ParentsComponent extends Component
{
    use ApiClientTrait;

    /**
     * Add parent to a folder.
     *
     * @param string $folderId Folder ID.
     * @param array $parents Parent objects as id/type pairs.
     * @return array
     */
    public function addRelated(string $folderId, array $parents): array
    {
        $results = [];
        foreach ($parents as $parent) {
            $results[] = $this->getClient()->replaceRelated($folderId, 'folders', 'parent', $parent);
        }

        return $results;
    }
}
