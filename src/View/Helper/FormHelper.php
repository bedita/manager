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

use Cake\Utility\Security;
use Cake\View\Helper\FormHelper as CakeFormHelper;

/**
 * Form helper.
 *
 * We extend CakePHP's component to replace check on session ID, if user is logged in with "remember me" feature.
 * This is necessary because the PHP session can expire and be renewed by Authentication component on form submission,
 * failing the form tampering check.
 */
class FormHelper extends CakeFormHelper
{
    /**
     * {@inheritDoc}
     */
    protected function _buildFieldToken($url, $fields, $unlockedFields = [])
    {
        $locked = [];
        foreach ($fields as $key => $value) {
            if (is_numeric($value)) {
                $value = (string)$value;
            }
            if (!is_int($key)) {
                $locked[$key] = $value;
                unset($fields[$key]);
            }
        }

        sort($unlockedFields, SORT_STRING);
        sort($fields, SORT_STRING);
        ksort($locked, SORT_STRING);
        $fields += $locked;

        $locked = implode('|', array_keys($locked));
        $unlocked = implode('|', $unlockedFields);

        // As part of form tampering, use user's token if user is already logged in, otherwise use session ID.
        $identifier = session_id();
        /** @var \Authentication\IdentityInterface|null $identity */
        $identity = $this->_View->getRequest()->getAttribute('identity');
        if (!empty($identity) && !empty($identity->get('token'))) {
            $identifier = $identity->get('token');
        }

        $hashParts = [
            $url,
            serialize($fields),
            $unlocked,
            $identifier,
        ];
        $fields = hash_hmac('sha1', implode('', $hashParts), Security::getSalt());

        return [
            'fields' => urlencode($fields . ':' . $locked),
            'unlocked' => urlencode($unlocked),
        ];
    }
}
