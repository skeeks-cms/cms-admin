<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.03.2015
 */
namespace skeeks\cms\modules\admin\components\settings;
use skeeks\cms\backend\BackendComponent;
use skeeks\cms\base\Component;
use skeeks\cms\base\Widget;
use skeeks\cms\components\Cms;
use skeeks\cms\helpers\UrlHelper;
use skeeks\cms\models\CmsLang;
use skeeks\cms\modules\admin\assets\AdminAsset;
use skeeks\cms\modules\admin\base\AdminDashboardWidget;
use skeeks\cms\modules\admin\components\Menu;
use skeeks\cms\modules\admin\components\UrlRule;
use skeeks\cms\modules\admin\dashboards\AboutCmsDashboard;
use skeeks\cms\modules\admin\dashboards\CmsInformDashboard;
use skeeks\cms\modules\admin\dashboards\ContentElementListDashboard;
use skeeks\cms\modules\admin\dashboards\DiscSpaceDashboard;
use skeeks\yii2\ckeditor\CKEditorPresets;
use yii\base\BootstrapInterface;
use yii\base\Theme;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @property CmsLang $cmsLanguage
 * @property [] $dasboardWidgets
 * @property [] $dasboardWidgetsLabels
 *
 * @property bool $requestIsAdmin
 * @property Menu $menu
 *
 * Class AdminSettings
 * @package skeeks\cms\modules\admin\components\settings
 */
class AdminSettings extends Component
{
    /**
     * Можно задать название и описание компонента
     * @return array
     */
    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name'          => \Yii::t('skeeks/cms','Setting the admin panel'),
        ]);
    }

    /**
     * @var Additional styling admin
     */
    public $asset;

    /**
     * @var array Components Desktops
     */
    public $dashboards = [];

    /**
     * @var array the list of IPs that are allowed to access this module.
     * Each array element represents a single IP filter which can be either an IP address
     * or an address with wildcard (e.g. 192.168.0.*) to represent a network segment.
     * The default value is `['127.0.0.1', '::1']`, which means the module can only be accessed
     * by localhost.
     */
    public $allowedIPs = ['*'];



    /**
     * Control via the admin interface
     */


    //Всплывающие окошки
    public $enableCustomConfirm     = Cms::BOOL_Y;
    public $enableCustomPromt       = Cms::BOOL_Y;

    //Языковые настройки
    public $languageCode            = "ru";

    //Настройки таблиц
    public $enabledPjaxPagination       = Cms::BOOL_Y;
    public $pageSize                    =   10;
    public $pageSizeLimitMin            =   1;
    public $pageSizeLimitMax            =   500;
    public $pageParamName               =   "page";

    //Настройки ckeditor
    public $ckeditorPreset              = CKEditorPresets::EXTRA;
    public $ckeditorSkin                = CKEditorPresets::SKIN_MOONO_COLOR;
    public $ckeditorHeight              = 400;
    public $ckeditorCodeSnippetGeshi    = Cms::BOOL_N;
    public $ckeditorCodeSnippetTheme    = 'monokai_sublime';

    public $blockedTime                 = 900; //15 минут



    /**
     * @return array
     */
    public function getDasboardWidgets()
    {
        $baseWidgets = [
            \Yii::t('skeeks/cms', 'Basic widgets') =>
            [
                AboutCmsDashboard::className(),
                CmsInformDashboard::className(),
                DiscSpaceDashboard::className(),
                ContentElementListDashboard::className(),
            ]
        ];

        $widgetsAll = ArrayHelper::merge($baseWidgets, $this->dashboards);

        $result = [];
        foreach ($widgetsAll as $label => $widgets)
        {
            if (is_array($widgets))
            {
                $resultWidgets = [];
                foreach ($widgets as $key => $classWidget)
                {
                    if (class_exists($classWidget) && is_subclass_of($classWidget, AdminDashboardWidget::className()))
                    {
                        $resultWidgets[$classWidget] = $classWidget;
                    }
                }

                $result[$label] = $resultWidgets;
            }

        }

        return $result;
    }

    /**
     * @return array
     */
    public function getDasboardWidgetsLabels()
    {
        $result = [];
        if ($this->dasboardWidgets)
        {
            foreach ($this->dasboardWidgets as $label => $widgets)
            {
                $resultWidgets = [];
                foreach ($widgets as $key => $widgetClassName)
                {
                    $resultWidgets[$widgetClassName] = (new $widgetClassName)->descriptor->name;
                }

                $result[$label] = $resultWidgets;
            }
        }

        return $result;
    }


    /**
     * @return boolean whether the module can be accessed by the current user
     */
    public function checkAccess()
    {
        $ip = \Yii::$app->getRequest()->getUserIP();

        foreach ($this->allowedIPs as $filter) {
            if ($filter === '*' || $filter === $ip || (($pos = strpos($filter, '*')) !== false && !strncmp($ip, $filter, $pos))) {
                return true;
            }
        }

        \Yii::warning('Access to Admin is denied due to IP address restriction. The requested IP is ' . $ip, __METHOD__);

        return false;
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['languageCode', 'pageParamName', 'enabledPjaxPagination'], 'string'],
            [['pageSize'], 'integer'],
            [['pageSizeLimitMin'], 'integer'],
            [['pageSizeLimitMax'], 'integer'],
            [['ckeditorCodeSnippetGeshi'], 'string'],
            [['ckeditorCodeSnippetTheme'], 'string'],
            [['enableCustomConfirm', 'enableCustomPromt', 'pageSize'], 'string'],
            [['ckeditorPreset', 'ckeditorSkin'], 'string'],
            [['ckeditorHeight'], 'integer'],
            [['blockedTime'], 'integer', 'min' => 300],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            //'asset'                             => \Yii::t('skeeks/cms','Additional css and js admin area'),
            'enableCustomConfirm'               => \Yii::t('skeeks/cms','Include stylized window confirmation (confirm)'),
            'enableCustomPromt'                 => \Yii::t('skeeks/cms','Include stylized window question with one field (promt)'),
            'languageCode'                      => \Yii::t('skeeks/cms','Interface language'),

            'pageParamName'                     => \Yii::t('skeeks/cms','Interface language'),

            'enabledPjaxPagination'             => \Yii::t('skeeks/cms','Turning ajax navigation'),
            'pageParamName'                     => \Yii::t('skeeks/cms','Parameter name pages, pagination'),
            'pageSize'                          => \Yii::t('skeeks/cms','Number of records on one page'),
            'pageSizeLimitMin'                  => \Yii::t('skeeks/cms','The maximum number of records per page'),
            'pageSizeLimitMax'                  => \Yii::t('skeeks/cms','The minimum number of records per page'),

            'ckeditorPreset'                    => \Yii::t('skeeks/cms','Instruments'),
            'ckeditorSkin'                      => \Yii::t('skeeks/cms','Theme of formalization'),
            'ckeditorHeight'                    => \Yii::t('skeeks/cms','Height'),
            'ckeditorCodeSnippetGeshi'          => \Yii::t('skeeks/cms','Use code highlighting') . ' (Code Snippets Using GeSHi)',
            'ckeditorCodeSnippetTheme'          => \Yii::t('skeeks/cms','Theme of {theme} code',['theme' => 'hightlight']),

            'blockedTime'                       => \Yii::t('skeeks/cms','Time through which block user'),
        ]);
    }



    public function renderConfigForm(ActiveForm $form)
    {
        echo \Yii::$app->view->renderFile(__DIR__ . '/_form.php', [
            'form'  => $form,
            'model' => $this
        ], $this);
    }

    /**
     * @param View|null $view
     */
    public function initJs(View $view = null)
    {
        $options =
        [
            'BlockerImageLoader'        => AdminAsset::getAssetUrl('images/loaders/circulare-blue-24_24.GIF'),
            'disableCetainLink'         => false,
            'globalAjaxLoader'          => true,
            'menu'                      => [],
        ];

        $options = \yii\helpers\Json::encode($options);

        \Yii::$app->view->registerJs(<<<JS
        (function(sx, $, _)
        {
            /**
            * Запускаем глобальный класс админки
            * @type {Admin}
            */
            sx.App = new sx.classes.Admin($options);

        })(sx, sx.$, sx._);
JS
        );
    }

    /**
     * Регистрация дополнительных asset
     * @param View $view
     * @return $this
     */
    public function registerAsset(View $view)
    {
        if ($this->asset)
        {
            if (class_exists($this->asset))
            {
                $className = $this->asset;
                $className::register($view);
            }
        }

        if ($this->enableCustomPromt == Cms::BOOL_Y)
        {
            $file = AdminAsset::getAssetUrl('js/classes/modal/Promt.js');
            //$file = \Yii::$app->assetManager->getAssetUrl(AdminAsset::register($view), 'js/classes/modal/Promt.js');
            \Yii::$app->view->registerJsFile($file,
            [
                'depends' => [AdminAsset::className()]
            ]);
        }

        if ($this->enableCustomConfirm == Cms::BOOL_Y)
        {
            $file = AdminAsset::getAssetUrl('js/classes/modal/Confirm.js');
            //$file = \Yii::$app->assetManager->getAssetUrl(AdminAsset::register($view), 'js/classes/modal/Confirm.js');
            \Yii::$app->view->registerJsFile($file,
            [
                'depends' => [AdminAsset::className()]
            ]);
        }
        return $this;
    }

    /**
     * layout пустой?
     * @return bool
     */
    public function isEmptyLayout()
    {
        if (UrlHelper::constructCurrent()->getSystem(\skeeks\cms\modules\admin\Module::SYSTEM_QUERY_EMPTY_LAYOUT))
        {
            return true;
        }

        return false;
    }

    /**
     * Настройки для Ckeditor, по умолчанию
     * @return array
     */
    public function getCkeditorOptions()
    {
        $clientOptions = [
            'height'                => $this->ckeditorHeight,
            'skin'                  => $this->ckeditorSkin,
            'codeSnippet_theme'     => $this->ckeditorCodeSnippetTheme,
        ];

        if ($this->ckeditorCodeSnippetGeshi == Cms::BOOL_Y)
        {
            $clientOptions['codeSnippetGeshi_url'] = '../lib/colorize.php';

            $preset = CKEditorPresets::getPresets($this->ckeditorPreset);
            $extraplugins = ArrayHelper::getValue($preset, 'extraPlugins', "");

            if ($extraplugins)
            {
                $extraplugins = explode(",", $extraplugins);
            }

            $extraplugins = array_merge($extraplugins, ['codesnippetgeshi']);
            $extraplugins = array_unique($extraplugins);

            $clientOptions['extraPlugins'] = implode(',', $extraplugins);
        }

        return [
            'preset' => $this->ckeditorPreset,
            'clientOptions' => $clientOptions
        ];
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getCmsLanguage()
    {
        return CmsLang::find()->where(['code' => \Yii::$app->language])->one();
    }


    /**
     * @return bool
     */
    public function getRequestIsAdmin()
    {
        if (BackendComponent::getCurrent() && BackendComponent::getCurrent()->controllerPrefix == 'admin')
        {
            return true;
        }

        return false;
    }

    /**
     * @var null|\skeeks\cms\modules\admin\components\Menu
     */
    protected $_menu = null;

    /**
     * @return null|\skeeks\cms\modules\admin\components\Menu
     * @throws \yii\base\InvalidConfigException
     */
    public function getMenu()
    {
        if (!$this->_menu)
        {
            $this->_menu = \Yii::createObject([
                'class' => 'skeeks\cms\modules\admin\components\Menu'
            ]);
        }

        return $this->_menu;
    }
}