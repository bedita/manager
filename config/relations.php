<?php
return [
    /**
     * Predefined object relations to consider in schema.
     *
     *  - `children` relation linking folders to objects
     */
    'DefaultRelations' => [

        'children' => [
            'type' => 'relations',
            'attributes' => [
                'name' => 'children',
                'inverse_name' => 'children',
                'label' => 'Folder children objects',
                'inverse_label' => 'Folder children objects',
            ],
            'left' => ['folders'],
            'right' => ['objects'],
        ],
    ],
];
