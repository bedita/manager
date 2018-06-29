<?php
return [
    /**
     * Default properties display configuration settings.
     * In `app.php` new settings can be introduced via `Properties` configuration
     * that will override this defuault settings.
     *
     * For each module name:
     *  - 'view' properties groups to present in object/resource view, where groups are:
     *      + '_keep' properties to keep even if not found in object
     *      + 'core' always open on the top
     *      + 'publish' publishing related
     *      + 'advanced' for power users
     *      + 'other' remaining attributes
     *  - 'index' properties to display in index view (other than id, status and modified for objects)
     */
    'DefaultProperties' => [
        'users' => [
            'view' => [
                '_keep' => [
                    'password',
                    'confirm-password'
                ],
                'core' => [
                    'username',
                    'password',
                    'confirm-password',
                    'name',
                    'surname',
                    'email',
                ],
            ],
            'index' => [
                'name',
                'surname',
                'username',
            ],
        ],

        // media
        'videos' => [
            'view' => [
                'advanced' => [
                    'extra',
                    'provider_extra',
                ],
            ],
        ],

        // user profile
        'user_profile' => [
            'view' => [
                'core' => [
                    'username',
                    'name',
                    'surname',
                    'email',
                    'person_title',
                    'gender',
                ],
                'advanced' => [
                    'phone',
                    'website',
                    'street_address',
                    'city',
                    'zipcode',
                    'country',
                    'state_name',
                ],
            ],
        ],

        // model/property_types
        'property_types' => [
            'view' => [
                'core' => [
                    'name',
                ],
            ],
            'index' => [
                'name',
                'params',
            ],
        ],

        // model/object_types
        'object_types' => [
            'view' => [
                'core' => [
                    'name',
                ],
            ],
            'index' => [
                'name',
                'enabled',
                'is_abstract',
            ],
        ],

        // model/relations
        'relations' => [
            'view' => [
                'core' => [
                    'name',
                    'label',
                    'inverse_name',
                    'inverse_label',
                    'description',
                    'params',
                ],
            ],
            'index' => [
                'name',
                'inverse_name',
            ],
        ],
    ],
];
