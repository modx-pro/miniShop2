<?php

if (!class_exists('msManagerController')) {
    require_once dirname(__FILE__, 2) . '/manager.class.php';
}

class Minishop2MgrUtilitesManagerController extends msManagerController
{
    /**
     * @return string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('ms2_utilites') . ' | miniShop2';
    }

    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['minishop2:default', 'minishop2:product', 'minishop2:manager'];
    }

    /**
     *
     */
    public function loadCustomCssJs()
    {

        $this->addCss($this->miniShop2->config['cssUrl'] . 'mgr/utilites/gallery.css');

        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/minishop2.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/utilites/panel.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/utilites/gallery/panel.js');
        $this->addJavascript($this->miniShop2->config['jsUrl'] . 'mgr/utilites/import/panel.js');

        $config = $this->miniShop2->config;

        // get source properties
        $productSource = $this->getOption('ms2_product_source_default', null, 1);
        if ($source = $this->modx->getObject('modMediaSource', $productSource)) {
            $config['utility_gallery_source_id'] = $productSource;
            $config['utility_gallery_source_name'] = $source->get('name');

            $properties =  $source->get('properties');
            $propertiesString = '';
            foreach (json_decode($properties['thumbnails']['value'], true) as $key => $value) {
                $propertiesString .= "<strong>$key: </strong>" . json_encode($value) . "<br>";
            }
            $config['utility_gallery_thumbnails'] = $propertiesString;
        }

        // get information about products and files
        $config['utility_gallery_total_products'] = $this->modx->getCount('msProduct', ['class_key' => 'msProduct']);
        $config['utility_gallery_total_products_files'] = $this->modx->getCount('msProductFile', ['parent' => 0]);

        // get params for import 
        $config['utility_import_fields'] = $this->getOption('ms2_utility_import_fields', null, 'article,pagetitle,price', true);
        $config['utility_import_fields_delimiter'] = $this->getOption('ms2_utility_import_fields_delimiter', null, ';', true);

        $this->addHtml(
            '<script>
            miniShop2.config = ' . json_encode($config) . ';

            Ext.onReady(function() {
                MODx.add({xtype: "minishop2-utilites"});
            });
        </script>'
        );
    }
}
