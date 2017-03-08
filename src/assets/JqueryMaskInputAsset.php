<?php
/**
 * jquery scrollbar plugin используетсся для красивого скроллбара справа
 * AppAsset
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 16.10.2014
 * @since 1.0.0
 */
namespace skeeks\cms\modules\admin\assets;
use skeeks\cms\base\AssetBundle;

/**
 * Class AppAsset
 * @package backend\assets
 */
class JqueryMaskInputAsset extends AssetBundle
{
    public $sourcePath = '@skeeks/cms/modules/admin/assets/plugins/jquery.maskedinput';

    public $css = [];

    public $js = [
        'dist/jquery.maskedinput.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
