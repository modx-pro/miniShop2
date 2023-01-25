<?php

class RemoveCatalogs
{
    public static function process(modX $modx, int $id)
    {
        $source = $modx->getObject('modMediaSource', $modx->getOption('ms2_product_source_default'));
        $props = $source->get('properties');
        $imgPath = MODX_BASE_PATH . $props['basePath']['value'] . $id;
        $modx->runProcessor('browser/directory/remove', ['dir' => $imgPath]);
    }
}
