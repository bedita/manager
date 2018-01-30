<?php
namespace App\View\Helper;

use Cake\View\Helper;

/**
 * Schema helper
 */
class SchemaHelper extends Helper
{

    /**
     * Infer control type from property schema.
     *
     * @param mixed $schema Property schema.
     * @return string
     */
    public function getControlTypeFromSchema($schema)
    {
        if (!is_array($schema)) {
            return 'text';
        }

        if (!empty($schema['oneOf'])) {
            foreach ($schema['oneOf'] as $subSchema) {
                if (!empty($subSchema['type']) && $subSchema['type'] === 'null') {
                    continue;
                }

                return $this->getControlTypeFromSchema($subSchema);
            }
        }

        if (empty($schema['type'])) {
            return 'text';
        }

        switch ($schema['type']) {
            case 'string':
                if (!empty($schema['format']) && $schema['format'] === 'date-time') {
                    return 'datetime';
                }
                if (!empty($schema['contentMediaType']) && $schema['contentMediaType'] === 'text/html') {
                    return 'textarea';
                }
                return 'text';

            case 'number':
            case 'integer':
                return 'number';

            case 'boolean':
                return 'checkbox';
        }

        return 'text';
    }
}
