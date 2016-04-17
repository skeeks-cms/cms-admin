<?php
return [

    'components' => [

        'admin' =>
        [
            'class' => '\skeeks\cms\modules\admin\components\settings\AdminSettings'
        ],

        'urlManager' => [
            'rules' => [
                'cms-admin' => [
                    "class" => 'skeeks\cms\modules\admin\components\UrlRule',
                    'adminPrefix' => '~sx'
                ],
            ]
        ]
    ],

    'modules' => [

        'admin' =>
        [
            'class' => '\skeeks\cms\modules\admin\Module'
        ],
    ],
];