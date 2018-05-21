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
    ],
],

'modules' => [

    'admin' =>
    [
        'class' => '\skeeks\cms\modules\admin\Module'
    ],
],

```

___

> [![skeeks!](https://skeeks.com/img/logo/logo-no-title-80px.png)](https://skeeks.com)  
<i>SkeekS CMS (Yii2) — quickly, easily and effectively!</i>  
[skeeks.com](https://skeeks.com) | [cms.skeeks.com](https://cms.skeeks.com)

