<?php

function gridViewSortableExpect($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$trait = file_get_contents(dirname(__DIR__).'/src/traits/GridViewSortableTrait.php');
$form = file_get_contents(dirname(__DIR__).'/src/widgets/gridView/_form.php');
$composer = json_decode(file_get_contents(dirname(__DIR__).'/composer.json'), true);

gridViewSortableExpect(
    ($composer['require']['skeeks/cms-backend'] ?? null) === '^2.0 || dev-master@dev',
    'cms-admin does not declare its backend adapter dependency.'
);
gridViewSortableExpect(
    strpos($trait, 'BackendSortableAdapterAsset::register($this->view);') !== false,
    'Sortable GridView does not register the backend adapter asset.'
);
gridViewSortableExpect(
    strpos($trait, 'sx.backend.sortable.create(') !== false
    && strpos($trait, 'itemSelector: "> tr"') !== false
    && strpos($trait, 'onUpdate: function(event)') !== false,
    'Sortable GridView does not use the normalized adapter contract.'
);
gridViewSortableExpect(
    strpos($trait, '.sortable(') === false
    && strpos($trait, 'yii\\jui\\Sortable') === false,
    'Sortable GridView still uses jQuery UI directly.'
);
gridViewSortableExpect(
    strpos($form, 'if ($columns || $columnsUrl) {') !== false
    && strpos($form, 'BackendSortableAdapterAsset::register($this);') !== false,
    'Grid column settings do not register the adapter conditionally.'
);
gridViewSortableExpect(
    strpos($form, 'sx.backend.sortable.create(this.JQueryVisibleSelected') !== false
    && strpos($form, 'itemSelector: "> li"') !== false
    && strpos($form, 'onUpdate: function()') !== false,
    'Grid column settings do not use the backend adapter.'
);
gridViewSortableExpect(
    strpos($form, '.sortable(') === false
    && strpos($form, '\\yii\\jui\\Sortable::widget()') === false,
    'Grid column settings still use jQuery UI directly.'
);

echo "CMS admin GridView sortable adapter contract: OK\n";
