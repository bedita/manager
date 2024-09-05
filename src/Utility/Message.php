<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Utility;

use BEdita\SDK\BEditaClientException;
use Cake\Utility\Hash;

/**
 * Utility class to handle system messages and translate them for the final user.
 */
class Message
{
    /**
     * Message main text.
     *
     * @var string
     */
    protected $title;

    /**
     * Message detail text.
     *
     * @var string
     */
    protected $detail;

    /**
     * Remap of error messages.
     *
     * @var array
     */
    protected $remap;

    /**
     * Constructor
     *
     * @param \BEdita\SDK\BEditaClientException $error Error object.
     */
    public function __construct(BEditaClientException $error)
    {
        $this->remap = [
            '[400] Invalid data' => __('Invalid data'),
            '[401] Unauthorized' => __('Unauthorized'),
            '[403] Forbidden' => __('Forbidden'),
            '[404] Not Found' => __('Not Found'),
            '[405] Method Not Allowed' => __('Method Not Allowed'),
            '[409] Conflict' => __('Conflict'),
            '[500] Internal Server Error' => __('Internal Server Error'),
            '[email is unique]: The provided value is invalid' => __('Email is in use'),
            '[username is unique]: The provided value is invalid' => __('Username is in use'),
            '[username is required]: This field is required' => __('Username is required'),
        ];
        $title = trim($error->getMessage());
        $this->title = Hash::get($this->remap, $title, __($title));
        $detail = Hash::get($error->getAttributes(), 'detail');
        if (empty($detail)) {
            return;
        }
        $matches = [];
        preg_match_all('/\[[a-zA-Z0-9._]+\]: [^[]+/', $detail, $matches);
        $details = [];
        foreach ($matches[0] as $matchDetail) {
            $details[] = $this->prepareDetail($matchDetail);
        }
        $this->detail = implode('. ', $details);
    }

    /**
     * Prepare detail message.
     *
     * @param string $detail Detail message.
     * @return string
     */
    public function prepareDetail(string $detail): string
    {
        $detail = trim($detail);
        $detail = str_replace('[0]: ', '', $detail);
        if (strpos($detail, '._required]') !== false) {
            $detail = str_replace('._required', ' is required', $detail);
            $detailonly = substr($detail, 1, strpos($detail, ']') - 1);

            return $this->remap[$detail] ?? $detailonly;
        }
        if (strpos($detail, '.unique]') !== false) {
            $detail = str_replace('.unique', ' is unique', $detail);
            $detailonly = str_replace('is unique', 'is in use', substr($detail, 1, strpos($detail, ']') - 1));

            return $this->remap[$detail] ?? $detailonly;
        }

        return $this->remap[$detail] ?? $detail;
    }

    /**
     * Get the message.
     *
     * @return string
     */
    public function get(): string
    {
        return empty($this->detail) ? $this->title : trim($this->title . '. ' . $this->detail);
    }
}
