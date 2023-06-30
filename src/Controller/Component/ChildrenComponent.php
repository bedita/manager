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
use Cake\Utility\Hash;

/**
 * Children component.
 * This component is used to add/remove children to a folder.
 * When a child is a subfolder, this use `PATCH /folders/:id/relationships/parent` to set parent to null.
 * When a child is not a subfolder, it's removed as usual by `removeRelated`.
 */
class ChildrenComponent extends Component
{
    use ApiClientTrait;

    /**
     * Add children to a folder.
     *
     * @param string $parentId Folder ID.
     * @param array $children Children objects as id/type pairs.
     * @return array
     */
    public function addRelated(string $parentId, array $children): array
    {
        $results = [];
        $actualRelated = $this->getClient()->get(sprintf('/objects?filter[parent]=%s', $parentId));
        $actualRelated = (array)Hash::extract($actualRelated, 'data.{n}.id');
        $index = 0;
        foreach ($children as $child) {
            // skip save when child is already related and in the same position
            if (!empty($actualRelated) && $actualRelated[$index++] === $child['id']) {
                continue;
            }
            $results[] = $this->addRelatedChild($parentId, $child);
        }

        return $results;
    }

    /**
     * Add single child by parent ID and child data.
     *
     * @param string $parentId The parent ID.
     * @param array $child The child data (id, type, meta).
     * @return array|null
     */
    public function addRelatedChild(string $parentId, array $child): ?array
    {
        $type = (string)Hash::get($child, 'type');
        if ($type !== 'folders') {
            return $this->getClient()->addRelated($parentId, 'folders', 'children', [$child]);
        }

        return $this->getClient()->addRelated($parentId, 'folders', 'parent', $child);
    }

    /**
     * Remove folder children.
     * When a child is a subfolder, this use `PATCH /folders/:id/relationships/parent` to set parent to null
     * When a child is not a subfolder, it's removed as usual by `removeRelated`
     *
     * @param string $parentId Folder ID
     * @param array $children Children objects as id/type pairs.
     * @return array
     */
    public function removeRelated(string $parentId, array $children): array
    {
        $results = [];
        foreach ($children as $child) {
            $results[] = $this->removeRelatedChild($parentId, $child);
        }

        return $results;
    }

    /**
     * Remove single child by parent ID and child data.
     *
     * @param string $parentId The parent ID.
     * @param array $child The child data (id, type, meta).
     * @return array|null
     */
    public function removeRelatedChild(string $parentId, array $child): ?array
    {
        $type = (string)Hash::get($child, 'type');
        if ($type === 'folders') {
            // invert relation call => use 'parent' relation on children folder
            return $this->getClient()->replaceRelated((string)Hash::get($child, 'id'), 'folders', 'parent', [null]);
        }

        return $this->getClient()->removeRelated($parentId, 'folders', 'children', [$child]);
    }
}
