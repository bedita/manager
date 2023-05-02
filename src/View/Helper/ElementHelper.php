<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Element helper
 */
class ElementHelper extends Helper
{
    /**
     * Return categories element via `Modules.<type>.categories._element` configuration
     *
     * @return string
     */
    public function categories(): string
    {
        $currentModule = (array)$this->getView()->get('currentModule');
        $name = (string)Hash::get($currentModule, 'name');
        $path = sprintf('Modules.%s.categories._element', $name);

        return (string)Configure::read($path, 'Form/categories');
    }

    /**
     * Return custom element via `Properties` configuration for
     * a relation or property group in current module.
     *
     * @param string $item Relation or group name
     * @param string $type Item type: `relation` or `group`
     * @return string
     */
    public function custom(string $item, string $type = 'relation'): string
    {
        $currentModule = (array)$this->getView()->get('currentModule');
        $name = (string)Hash::get($currentModule, 'name');
        if ($type === 'relation') {
            $path = sprintf('Properties.%s.relations._element.%s', $name, $item);
        } else {
            $path = sprintf('Properties.%s.view.%s._element', $name, $item);
        }

        return (string)Configure::read($path);
    }

    /**
     * Return sidebar element via `Modules.<type>.sidebar._element` configuration
     *
     * @return string
     */
    public function sidebar(): string
    {
        $currentModule = (array)$this->getView()->get('currentModule');
        $name = (string)Hash::get($currentModule, 'name');
        $path = sprintf('Modules.%s.sidebar._element', $name);

        return (string)Configure::read($path, 'Modules/sidebar');
    }
}
