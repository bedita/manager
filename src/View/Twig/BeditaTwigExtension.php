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

namespace App\View\Twig;

use Cake\Core\Configure;

/**
 * BEdita Twig extension class.
 *
 * Provide BEdita utils to Twig view.
 */
class BeditaTwigExtension extends \Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName() : string
    {
        return 'bedita';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('config', [Configure::class, 'read']),
            new \Twig_SimpleFunction('write_config', [Configure::class, 'write']),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters() : array
    {
        return [
            new \Twig_SimpleFilter(
                'shuffle',
                function (array $array) {
                    shuffle($array);

                    return $array;
                }
            ),
        ];
    }
}
