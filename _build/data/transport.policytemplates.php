<?php

/** @var modX $modx */
$templates = [];

$tmp = [
    'miniShopManagerPolicyTemplate' => [
        'description' => 'A policy for miniShop2 managers.',
        'template_group' => 1,
        'permissions' => [
            'mscategory_save' => [],
            'msproduct_save' => [],
            'msproduct_publish' => [],
            'msproduct_delete' => [],
            'msorder_save' => [],
            'msorder_view' => [],
            'msorder_list' => [],
            'msorder_remove' => [],
            'mssetting_save' => [],
            'mssetting_view' => [],
            'mssetting_list' => [],
            'msproductfile_save' => [],
            'msproductfile_generate' => [],
            'msproductfile_list' => [],
        ],
    ],
];

foreach ($tmp as $k => $v) {
    $permissions = [];

    if (isset($v['permissions']) && is_array($v['permissions'])) {
        foreach ($v['permissions'] as $k2 => $v2) {
            /** @var modAccessPermission $event */
            $permission = $modx->newObject('modAccessPermission');
            $permission->fromArray(
                array_merge([
                    'name' => $k2,
                    'description' => $k2,
                    'value' => true,
                ], $v2),
                '',
                true,
                true
            );
            $permissions[] = $permission;
        }
    }

    /** @var $template modAccessPolicyTemplate */
    $template = $modx->newObject('modAccessPolicyTemplate');
    $template->fromArray(
        array_merge([
            'name' => $k,
            'lexicon' => PKG_NAME_LOWER . ':permissions',
        ], $v),
        '',
        true,
        true
    );

    if (!empty($permissions)) {
        $template->addMany($permissions);
    }
    $templates[] = $template;
}

return $templates;
