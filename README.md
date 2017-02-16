Admin controll panel for SkeekS CMS
===================================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist skeeks/cms-admin "*"
```

or add

```
"skeeks/cms-admin": "*"
```

Configuration app
----------

```php

'components' => [

    'admin' =>
    [
        'class' => '\skeeks\cms\modules\admin\components\settings\AdminSettings',
        'allowedIPs' => ['91.219.167.*', '111.*']
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

```

___

> [![skeeks!](https://gravatar.com/userimage/74431132/13d04d83218593564422770b616e5622.jpg)](https://skeeks.com)
<i>SkeekS CMS (Yii2) — quickly, easily and effectively!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)


