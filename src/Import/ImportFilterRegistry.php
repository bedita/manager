<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Import;

use App\Core\Filter\ImportFilter;
use App\Core\SingletonTrait;
use Cake\Core\App;
use Cake\Core\ObjectRegistry;
use Cake\Core\StaticConfigTrait;

/**
 * Registry for import filter classes.
 */
class ImportFilterRegistry extends ObjectRegistry
{

    use SingletonTrait;
    use StaticConfigTrait;

    /**
     * An array mapping url schemes to fully qualified import filter classes
     *
     * @var array
     */
    protected static $_dsnClassMap = [
    ];

    /**
     * {@inheritDoc}
     */
    protected function _resolveClassName($class)
    {
        if (is_object($class)) {
            return $class;
        }

        return App::className($class, 'Import', 'ImportFilter');
    }

    /**
     * {@inheritDoc}
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new \BadMethodCallException(sprintf('Import filter %s is not available.', $class));
    }

    /**
     * {@inheritDoc}
     */
    protected function _create($class, $alias, $config)
    {
        if (is_object($class)) {
            $instance = $class;
        }

        unset($config['className']);
        if (!isset($instance)) {
            $instance = new $class($config);
        }

        if (!($instance instanceof ImportFilter)) {
            throw new \RuntimeException(
                sprintf('Import filter must use %s as a base class.', ImportFilter::class)
            );
        }

        if (!$instance->initialize()) {
            throw new \RuntimeException(
                sprintf('Import filter  %s is not properly configured.', get_class($instance))
            );
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     *
     * @return \App\Core\Filter\ImportFilter|null
     */
    public function get($name)
    {
        /* @var \App\Core\Filter\ImportFilter|null $filter */
        $filter = parent::get($name);
        if ($filter !== null || !in_array($name, static::configured())) {
            return $filter;
        }

        return $this->load($name, static::getConfig($name));
    }
}
