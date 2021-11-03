<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Categories helper
 *
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \App\View\Helper\PropertyHelper $Property
 */
class CategoriesHelper extends Helper
{
    public $helpers = ['Form', 'Property'];

    /**
     * Control for categories
     *
     * @param string $name The name
     * @param mixed|null $value The value
     * @return string
     */
    public function control(string $name, $value): string
    {
        $options = ['label' => false];
        if (!$this->isTree()) {
            return sprintf(
                '<div class="categories"><h3>%s</h3>%s</div>',
                __('Global'),
                $this->Property->control($name, $value, $options)
            );
        }

        return $this->html($name, $value, $options);
    }

    /**
     * Html for categories tree
     *
     * @param string $name The name
     * @param mixed|null $value The value
     * @param array $options The options
     * @return string
     */
    public function html(string $name, $value, array $options): string
    {
        $html = '';
        $tree = $this->tree();
        $hiddenField = true; // hiddenField false on all controls except the first
        foreach ($tree as $node) {
            $html .= $this->node($node, $name, $value, $options, $hiddenField);
        }

        return $html;
    }

    /**
     * Html for single node of categories tree
     *
     * @param array $node The category node
     * @param string $name The name
     * @param mixed|null $value The value
     * @param array $options The options
     * @param bool $hiddenField The hiddenField flag
     * @return string
     */
    public function node(array $node, string $name, $value, array $options, bool &$hiddenField): string
    {
        $controlOptions = $this->controlOptions($node, $value, $options, $hiddenField);
        $title = count(array_keys($controlOptions['options'])) === 1 ? __('Global') : $node['label'];

        return sprintf(
            '<div class="categories"><h3>%s</h3>%s</div>',
            $title,
            $this->Form->control($name, $controlOptions)
        );
    }

    /**
     * Control options for category node.
     *
     * @param array $node The category node
     * @param mixed|null $value The value
     * @param array $options The options
     * @param bool $hiddenField The hiddenField flag
     * @return array
     */
    public function controlOptions(array $node, $value, array $options, bool &$hiddenField): array
    {
        $controlOptions = $options + [
            'type' => 'select',
            'multiple' => 'checkbox',
            'value' => (array)Hash::extract((array)$value, '{n}.name'),
            'hiddenField' => $hiddenField,
        ];
        $hiddenField = false;
        if (empty($node['children'])) {
            $controlOptions['options'][0] = ['value' => $node['name'], 'text' => $node['label']];

            return $controlOptions;
        }
        foreach ($node['children'] as $key => $child) {
            $controlOptions['options'][$key] = ['value' => $child['name'], 'text' => $child['label']];
        }

        return $controlOptions;
    }

    /**
     * Categories tree.
     * Return roots with children in 'children'.
     *
     * @return array
     */
    public function tree(): array
    {
        $schema = (array)$this->_View->get('schema');
        $categories = (array)Hash::get($schema, 'categories');
        if (empty($categories)) {
            return [];
        }

        $roots = [];
        foreach ($categories as $category) {
            if (empty($category['parent_id'])) { // root
                $roots[$category['id']] = $category;
            } else { // child
                $roots[$category['parent_id']]['children'][] = $category;
            }
        }

        return $roots;
    }

    /**
     * Return true when a category parent id is not null, among schema.categories.
     * False otherwise.
     *
     * @return bool
     */
    public function isTree(): bool
    {
        $schema = (array)$this->_View->get('schema');
        $categories = (array)Hash::get($schema, 'categories');
        if (empty($categories)) {
            return false;
        }
        foreach ($categories as $category) {
            if (!empty($category['parent_id'])) {
                return true;
            }
        }

        return false;
    }
}
