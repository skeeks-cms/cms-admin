<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010-2014 SkeekS (Sx)
 * @date 30.01.2015
 * @since 1.0.0
 */
/* @var $this yii\web\View */

use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab as ActiveForm;


$autoEnvFile = '';
if (file_exists(APP_ENV_GLOBAL_FILE))
{
    $autoEnvFile = \Yii::t('app','Yes').' ';
    $autoEnvFile .= "<a class='btn btn-xs btn-primary' href='" . \skeeks\cms\helpers\UrlHelper::construct('admin/info/remove-env-global-file')->enableAdmin()->toString() . "'>".\Yii::t('app','Delete')."</a>  ";
} else
{
    $autoEnvFile = \Yii::t('app','No').' ';
}
$autoEnvFile .= "<a class='btn btn-xs btn-primary' href='" . \skeeks\cms\helpers\UrlHelper::construct('admin/info/write-env-global-file', ['env' => 'dev'])->enableAdmin()->toString() . "'>".\Yii::t('app', 'To record')." dev</a>  ";
$autoEnvFile .= "<a class='btn btn-xs btn-primary' href='" . \skeeks\cms\helpers\UrlHelper::construct('admin/info/write-env-global-file', ['env' => 'prod'])->enableAdmin()->toString() . "'>".\Yii::t('app', 'To record')." prod</a>";

?>
<? $form = ActiveForm::begin(); ?>

<?= $form->fieldSet(\Yii::t('app','Project configuration')); ?>
    <?php
    echo $this->render('table', [
        'values' => [
            'SkeekS CMS' => \Yii::$app->cms->descriptor->version,

            \Yii::t('app','{yii} Version', ['yii' => 'Yii']) => $application['yii'],
            \Yii::t('app','Project name') => $application['name'] . " (<a href='" . \skeeks\cms\helpers\UrlHelper::construct('cms/admin-settings')->enableAdmin()->toString() . "'>".\Yii::t('app','edit')."</a>)",
            \Yii::t('app','Environment ({yii_env})',['yii_env' => 'YII_ENV']) => $application['env'],
            \Yii::t('app','Development mode ({yii_debug})',['yii_debug' => 'YII_DEBUG']) => $application['debug'] ? \Yii::t('app','Yes') : \Yii::t('app','No'),
            \Yii::t('app',"Checks environment variables").' (APP_ENV_GLOBAL_FILE)' => $autoEnvFile . " <a class='btn btn-xs btn-default' title='" . APP_ENV_GLOBAL_FILE . "'>i</a>"

            ,
        ],
    ]);
    ?>
<?= $form->fieldSetEnd(); ?>


<?= $form->fieldSet(\Yii::t('app','All extensions and modules {yii}',['yii' => 'Yii'])); ?>
    <?if (!empty($extensions)) {
        echo $this->render('table', [
            'values' => $extensions,
        ]);
    }?>
<?= $form->fieldSetEnd(); ?>


<?= $form->fieldSet(\Yii::t('app','{php} configuration',['php' => "PHP"])); ?>
    <?
    echo $this->render('table', [
        'values' => [
            'PHP Version' => $php['version'],
            'Xdebug' => $php['xdebug'] ? 'Enabled' : 'Disabled',
            'APC' => $php['apc'] ? 'Enabled' : 'Disabled',
            'Memcache' => $php['memcache'] ? 'Enabled' : 'Disabled',
            'Xcache' => $php['xcache'] ? 'Enabled' : 'Disabled',
            'Gd' => $php['gd'] ? 'Enabled' : 'Disabled',
            'Imagick' => $php['imagick'] ? 'Enabled' : 'Disabled',
            'Sendmail Path' => ini_get('sendmail_path'),
            'Sendmail From' => ini_get('sendmail_from'),
            'open_basedir' => ini_get('open_basedir'),
            'realpath_cache_size' => ini_get('realpath_cache_size'),
            'xcache.cacher' => ini_get('xcache.cacher'),
            'xcache.ttl' => ini_get('xcache.ttl'),
            'xcache.stat' => ini_get('xcache.stat'),
            'xcache.size' => ini_get('xcache.size'),
        ],
    ]);
    ?>
<?= $form->fieldSetEnd(); ?>


<?= $form->fieldSet('PHP info'); ?>
    <iframe id="php-info" src='<?= \skeeks\cms\helpers\UrlHelper::construct('/admin/info/php')->enableAdmin()->toString(); ?>' width='100%' height='1000'></iframe>;
<?= $form->fieldSetEnd(); ?>

<? ActiveForm::end(); ?>




