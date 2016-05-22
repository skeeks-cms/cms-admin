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
        ],

        'i18n' => [
            'translations' =>
            [
                'skeeks/admin' => [
                    'class'             => 'yii\i18n\PhpMessageSource',
                    'basePath'          => '@skeeks/cms/modules/admin/messages',
                    'fileMap' => [
                        'skeeks/admin' => 'main.php',
                    ],
                ]
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