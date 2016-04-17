<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 30.03.2015
 */
namespace skeeks\cms\modules\admin\components\settings;
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
use yii\helpers\ArrayHelper;
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
            'name'          => \Yii::t('app','Setting the admin panel'),
        ]);
    }

    public $asset;

    public $dashboards = [];

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
            \Yii::t('app', 'Basic widgets') =>
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


    public function init()
    {
        parent::init();

        \Yii::$app->language = $this->languageCode;
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
            //'asset'                             => \Yii::t('app','Additional css and js admin area'),
            'enableCustomConfirm'               => \Yii::t('app','Include stylized window confirmation (confirm)'),
            'enableCustomPromt'                 => \Yii::t('app','Include stylized window question with one field (promt)'),
            'languageCode'                      => \Yii::t('app','Interface language'),

            'pageParamName'                     => \Yii::t('app','Interface language'),

            'enabledPjaxPagination'             => \Yii::t('app','Turning ajax navigation'),
            'pageParamName'                     => \Yii::t('app','Parameter name pages, pagination'),
            'pageSize'                          => \Yii::t('app','Number of records on one page'),
            'pageSizeLimitMin'                  => \Yii::t('app','The maximum number of records per page'),
            'pageSizeLimitMax'                  => \Yii::t('app','The minimum number of records per page'),

            'ckeditorPreset'                    => \Yii::t('app','Instruments'),
            'ckeditorSkin'                      => \Yii::t('app','Theme of formalization'),
            'ckeditorHeight'                    => \Yii::t('app','Height'),
            'ckeditorCodeSnippetGeshi'          => \Yii::t('app','Use code highlighting') . ' (Code Snippets Using GeSHi)',
            'ckeditorCodeSnippetTheme'          => \Yii::t('app','Theme of {theme} code',['theme' => 'hightlight']),

            'blockedTime'                       => \Yii::t('app','Time through which block user'),
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
        return CmsLang::find()->where(['code' => $this->languageCode])->one();
    }


    protected $_requestIsAdmin = null;

    /**
     * @return bool
     */
    public function getRequestIsAdmin()
    {
        if ($this->_requestIsAdmin !== null)
        {
            return $this->_requestIsAdmin;
        }

        if (\Yii::$app->urlManager->rules)
        {
            foreach (\Yii::$app->urlManager->rules as $rule)
            {
                if ($rule instanceof UrlRule)
                {
                    $urlRuleAdmin = $rule;

                    $request        = \Yii::$app->request;
                    $pathInfo       = $request->getPathInfo();
                    $params         = $request->getQueryParams();
                    $firstPrefix    = substr($pathInfo, 0, strlen($urlRuleAdmin->adminPrefix));

                    if ($firstPrefix == $urlRuleAdmin->adminPrefix)
                    {
                        $this->_requestIsAdmin = true;
                        return $this->_requestIsAdmin;
                    }
                }
            }
        }

        $this->_requestIsAdmin = false;
        return $this->_requestIsAdmin;
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