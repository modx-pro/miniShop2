<?php

class msProductFileGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msProductFile';
    public $languageTopics = array('default', 'minishop2:product');
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';
    public $permission = 'msproductfile_list';
    /** @var miniShop2 $miniShop2 */
    protected $miniShop2;


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        $this->miniShop2 = $this->modx->getService('miniShop2');

        return parent::initialize();
    }


    /**
     * @return array|string
     */
    public function process() {
        $beforeQuery = $this->beforeQuery();
        if ($beforeQuery !== true) {
            return $this->failure($beforeQuery);
        }
        $data = $this->getData();

        return $this->outputArray($data['results'], $data['total']);
    }


    /**
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        $data['results'] = array();
        if ($c->prepare() && $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                $data['results'][] = $this->prepareArray($row);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($c->stmt->errorInfo(), true));
        }

        return $data;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modMediaSource', 'Source');
        $c->leftJoin($this->classKey, 'Thumb',
            $this->classKey . '.id = Thumb.parent AND 
            Thumb.path LIKE "%' . $this->modx->getOption('ms2_product_thumbnail_size', null, '120x90', true) . '/%"'
        );
        $c->groupby($this->classKey . '.id, thumbnail');
        $c->select('Source.name as source_name');
        $c->select('Thumb.url as thumbnail');

        $c->where(array('product_id' => $this->getProperty('product_id')));

        $parent = $this->getProperty('parent');
        if ($parent !== false) {
            $c->where(array('parent' => $parent));
        }
        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            $c->where(array(
                'file:LIKE' => "%{$query}%",
                'OR:name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }


    /**
     * @param array $row
     *
     * @return array
     */
    public function prepareArray(array $row)
    {

        if (empty($row['thumbnail'])) {
            if ($row['type'] != 'image') {
                $row['thumbnail'] = (file_exists(MODX_ASSETS_PATH . 'components/minishop2/img/mgr/extensions/' . $row['type'] . '.png'))
                    ? MODX_ASSETS_URL . 'components/minishop2/img/mgr/extensions/' . $row['type'] . '.png'
                    : MODX_ASSETS_URL . 'components/minishop2/img/mgr/extensions/other.png';
            } else {
                $row['thumbnail'] = $this->miniShop2->config['defaultThumb'];
            }
        }

        $row['properties'] = strpos($row['properties'], '{') === 0
            ? json_decode($row['properties'], true)
            : array();

        $row['actions'] = array();

        $row['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('ms2_gallery_file_update'),
            'action' => 'updateFile',
            'button' => false,
            'menu' => true,
        );

        $row['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-share',
            'title' => $this->modx->lexicon('ms2_gallery_file_show'),
            'action' => 'showFile',
            'button' => false,
            'menu' => true,
        );

        if ($row['type'] == 'image') {
            $row['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-refresh',
                'title' => $this->modx->lexicon('ms2_gallery_file_generate_thumbs'),
                'multiple' => $this->modx->lexicon('ms2_gallery_file_generate_thumbs'),
                'action' => 'generateThumbs',
                'button' => false,
                'menu' => true,
            );
        }

        $row['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('ms2_gallery_file_delete'),
            'multiple' => $this->modx->lexicon('ms2_gallery_file_delete_multiple'),
            'action' => 'deleteFiles',
            'button' => false,
            'menu' => true,
        );

        return $row;
    }

}

return 'msProductFileGetListProcessor';