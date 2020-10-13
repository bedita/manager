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
                'label' => 'Folder children',
                'inverse_label' => 'Folder children',
            ],
            'left' => ['folders'],
            'right' => ['objects'],
        ],
    ],
];
