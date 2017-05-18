<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 27.04.2016
 */
namespace skeeks\cms\modules\admin\widgets\filters;

use skeeks\cms\base\AssetBundle;

/**
 * Class SelectLanguage
 * @package common\widgets\selectLanguage
 */
class AdminFiltersFormAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/modules/admin/widgets/filters/assets';

    public $css = [
        'filters-form.css',
    ];

    public $js = [
        'filters-form.js',
    ];

    public $depends =
    [
        'yii\web\YiiAsset',
        '\skeeks\sx\assets\Custom',
        '\skeeks\sx\assets\ComponentAjaxLoader',
    ];
}