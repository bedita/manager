<?php
namespace App\Test\Utils;

use App\Controller\Component\ModulesComponent;

class MyModulesComponent extends ModulesComponent
{
    /**
     * Mock oEmbed meta
     *
     * @var array
     */
    public $meta = [];

    protected function oEmbedMeta(string $url): ?array
    {
        return $this->meta;
    }

    public function objectTypes(?bool $abstract = null): array
    {
        return ['mices', 'elefants', 'cats', 'dogs'];
    }
}
