<?php
/**
 * @Author: Anton Jukov
 */
if (!empty($scriptProperties['vendor'])) {
    $vendor = $modx->getObject('msVendor', $scriptProperties['vendor']);

    //Check vendor exist
    if (is_object($vendor)) {
        //Get fields data
        $vendor = $vendor->_fields;

        //Get content
        if ($scriptProperties['includeContent'] == 1) {
            if (!empty($vendor['resource'])) {
                $resource = $modx->getObject('modResource', $vendor['resource']);

                if (is_object($resource)) {
                    $vendor['pagetitle'] = $resource->get('pagetitle');
                    $vendor['introtext'] = $resource->get('introtext');
                    $vendor['content'] = $resource->get('content');
                }
            }
        }

        //How to return?
        if ($scriptProperties['returnData'] == 1) {
            if (!empty($scriptProperties['returnOption'])) {
                return $vendor[$scriptProperties['returnOption']];
            }
            else {
                return $vendor;
            }
        }
        else {
            $output = $modx->getChunk($scriptProperties['tpl'], $vendor);
            return $output;
        }
    }
    else {
        return false;
    }
}
else {
    return false;
}