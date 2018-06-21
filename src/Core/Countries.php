<?php

namespace App\Core;

use Cake\Utility\Hash;
use League\ISO3166\ISO3166;

/**
 * Countries class
 * @uses League\ISO3166\ISO3166 to get countries data
 */
class Countries
{
    /**
     * Return list of countries pairs <code> =>  >name> by specified code
     *
     * @param string $code The code type: can be alpha2, alpha3, numeric, name
     * @return array The countries list
     */
    public function list($code = 'alpha3') : array
    {
        $all = (new ISO3166)->all();

        return Hash::combine($all, sprintf('{n}.%s', $code), '{n}.name');
    }
}
