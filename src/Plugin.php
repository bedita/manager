<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App;

use Cake\Core\Plugin as CakePlugin;

/**
 * {@inheritDoc}
 *
 * Extended with BEdita plugins utilities
 */
class Plugin extends CakePlugin
{
    /**
     * Loaded BEdita Manager application plugins
     * Auxiliary & internally loaded plugins like `DebugKit`, `Bake` and `TwigView` are exluded
     *
     * @return array
     */
    public static function loadedAppPlugins(): array
    {
        $sysPlugins = [
            'Authentication',
            'Bake',
            'DebugKit',
            'IdeHelper',
            'Cake/Repl',
            'BEdita/WebTools',
            'BEdita/I18n',
            'Migrations',
            'Cake/TwigView',
        ];

        return array_values(array_diff((array)static::loaded(), $sysPlugins));
    }
}
