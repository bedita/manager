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
namespace App\View\Helper;

use App\ApiClientProvider;
use App\View\Helper\ThumbHelper;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Helper to view multimedia object or streams in common <img>, <video> or other html tags
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \App\View\Helper\ThumbHelper $Thumb
 */
class MediaHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html', 'Thumb'];

    /**
     * Relative URL of image to display in case of error
     *
     * @var string
     */
    public const IMG_URL_ERROR = '/img/iconMissingImage.gif';

    /**
     * Relative URL of image to display when thumb is not ready
     *
     * @var string
     */
    public const IMG_URL_WAIT = '/img/iconMissingImage.gif';

    /**
     * HTML helper
     *
     * @var Cake\View\Helper\HtmlHelper
     */
    protected $defaults = [
        'providerThumb' => false,
        // provide link to original media URL
        'mediaUrl' => null,
        // thumbnail preset
        'preset' => null,
        // thumbnail options
        'options' => [],
    ];

    /**
     * View a media object with HTML tags
     *
     * `$options` array may include:
     *
     *  + `providerThumb` (default false) boolean flag, display provider thumbnail if avilable
     *          instead of actual media or thumbnail
     *  + `preset` thumbnail preset to use
     *  + `options` thumbnail options to use including `w` (width) and `h` (height)
     *
     * @param array $media The Media object data.
     * @param array|null $streams Included streams data.
     * @param array $options Display options.
     * @return string An HTML view of the media object.
     */
    public function view(array $media, ?array $streams = [], array $options = []): string
    {
        $options = array_merge($this->defaults, $options);

        $providerThumb = Hash::get($media, 'attributes.provider_thumbnail');
        if ($providerThumb && $options['providerThumb']) {
            return $this->providerThumb($providerThumb, $options['options']);
        }

        // return HTML view/player if available
        $providerHtml = Hash::get($media, 'attributes.provider_extra.html');
        if ($providerHtml) {
            return $providerHtml;
        }

        if ($media['type'] != 'images') {
            return '';
        }
        unset($options['providerThumb']);
        if ($options['mediaUrl']) {
            $options['mediaUrl'] = Hash::get($streams, '0.meta.url');
        }

        return $this->image($media, $options);
    }

    /**
     * Display the external provider thumbnail
     *
     * @param string $providerThumb The provider thumbnail URL.
     * @param array $options Display options.
     * @return string An HTML view of the media object.
     */
    protected function providerThumb(string $providerThumb, array $options): string
    {
        $attributes = ['fullBase' => true];
        if (!empty($options['w'])) {
            $attributes['width'] = $options['w'];
        }
        if (!empty($options['h'])) {
            $attributes['height'] = $options['h'];
        }

        return $this->Html->image($providerThumb, $attributes);
    }

    /**
     * Display image thumbnail in <img> tag, wrap with <a> if a `mediaUrl` is passed via $options
     *
     * @param array $image Image objcet data.
     * @param array $options Display options.
     * @return string An HTML view of the media object.
     */
    protected function image(array $image, array $options): string
    {
        $mediaUrl = $options['mediaUrl'];
        unset($options['mediaUrl']);
        $url = $this->thumb($image['id'], $options);
        if ($url === ThumbHelper::ERROR || $url === ThumbHelper::NOT_ACCEPTABLE) {
            $url = self::IMG_URL_ERROR;
        } elseif ($url === ThumbHelper::NOT_READY) {
            $url = self::IMG_URL_WAIT;
        }

        $img = $this->Html->image($url);
        if (!$mediaUrl) {
            return $img;
        }

        return $this->Html->link($img, $mediaUrl, ['escape' => false, 'target' => '_blank']);
    }

    /**
     * Get thumbnail via Thumb Helper
     *
     * @param string|int $imageId Image id,
     * @param array $options Thumbnail options
     * @return string|int
     * @codeCoverageIgnore
     */
    protected function thumb($imageId, array $options)
    {
        return $this->Thumb->url($imageId, $options);
    }
}
