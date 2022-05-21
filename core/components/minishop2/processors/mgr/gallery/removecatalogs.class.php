<?php

class RemoveCatalogs
{
    public static function process(Modx $modx, int $id)
    {
        $source = $modx->getObject('modMediaSource', $modx->getOption('ms2_product_source_default'));
        $source = $source->toArray();
        $imgPath = MODX_BASE_PATH . $source['properties']['basePath']['value'] . $id;
        if(file_exists($imgPath)){
            RemoveCatalogs::remove_dir($imgPath);
            $modx->log(1, print_r($imgPath,1));
        }

    }

    public static function remove_dir($dir)
    {
        if ($objs = glob($dir . '/*')) {
            foreach($objs as $obj) {
                is_dir($obj) ? RemoveCatalogs::remove_dir($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

}