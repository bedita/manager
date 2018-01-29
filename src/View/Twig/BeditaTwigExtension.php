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

/**
 * Bedita Twig extension class.
 * Provide bedita utils to twig view.
 */
class BeditaTwigExtension extends \Twig_Extension
{
    /**
     * Get declared functions.
     *
     * @return \Twig_SimpleFunction[]
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('config', 'Cake\Core\Configure::read'),
        ];
    }

    /**
     * Get extension name.
     *
     * @return string extension name.
     */
    public function getName()
    {
        return 'bedita';
    }
}
