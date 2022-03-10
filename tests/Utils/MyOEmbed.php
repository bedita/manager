<?php
namespace App\Test\Utils;

use App\Utility\OEmbed;

class MyOEmbed extends OEmbed
{
    /**
     * Mock JSON response
     *
     * @var array
     */
    public $json = [];

    protected function fetchJson(string $oembedUrl): array
    {
        return $this->json;
    }
}
