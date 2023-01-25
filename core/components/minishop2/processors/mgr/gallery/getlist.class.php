<?php

class msProductFileGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msProductFile';
    public $languageTopics = ['default', 'minishop2:product'];
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'ASC';
    public $permission = 'msproductfile_list';
    /** @var miniShop2 $miniShop2 */
    protected $miniShop2;
    protected $thumb;

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        $this->miniShop2 = $this->modx->getService('miniShop2');

        /** @var msProduct $product */
        $product = $this->modx->getObject('msProduct', (int)$this->getProperty('product_id'));
        if ($product) {
            $data = $product->getOne('Data');
            if ($data) {
                /** @var modMediaSource $source */
                if ($source = $this->modx->getObject('modMediaSource', (int)$data->get('source'))) {
                    $properties = $source->getProperties();
                    $thumbnails = [];
                    if (!empty($properties['thumbnails']['value'])) {
                        $thumbnails = json_decode($properties['thumbnails']['value'], true);
                    } elseif (!empty($properties['thumbnail']['value'])) {
                        $thumbnails = json_decode($properties['thumbnail']['value'], true);
                    }
                    if (!empty($thumbnails)) {
                        foreach ($thumbnails as $key => $thumb) {
                            if (!is_numeric($key)) {
                                $this->thumb = $key;
                            } elseif (!empty($thumb['w']) || !empty($thumb['h'])) {
                                $this->thumb = @$thumb['w'] . 'x' . @$thumb['h'];
                            }
                            break;
                        }
                    }
                }
            }
        }
        if (!$this->thumb) {
            $this->thumb = $this->modx->getOption('ms2_product_thumbnail_size', null, 'small', true);
        }

        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function process()
    {
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
        $data = [];
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns(
            $sortClassKey,
            $this->getProperty('sortAlias', $sortClassKey),
            '',
            [$this->getProperty('sort')]
        );
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        $data['results'] = [];
        if ($c->prepare() && $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                $data['results'][] = $this->prepareArray($row);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($c->stmt->errorInfo(), true));
        }

        return $data;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->where([
            'parent' => (int)$this->getProperty('parent'),
            'product_id' => (int)$this->getProperty('product_id'),
        ]);

        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            $c->where([
                'file:LIKE' => "%{$query}%",
                'OR:name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ]);
        }

        return $c;
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $c->leftJoin('modMediaSource', 'Source');
        $c->leftJoin(
            $this->classKey,
            'Thumb',
            $this->classKey . '.id = Thumb.parent AND
            Thumb.path LIKE "%/' . $this->thumb . '/"'
        );
        $c->select('Source.name as source_name, Thumb.url as thumbnail');
        $c->groupby($this->classKey . '.id, thumbnail');

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
                $row['thumbnail'] = (file_exists(
                    MODX_ASSETS_PATH . 'components/minishop2/img/mgr/extensions/' . $row['type'] . '.png'
                ))
                    ? MODX_ASSETS_URL . 'components/minishop2/img/mgr/extensions/' . $row['type'] . '.png'
                    : MODX_ASSETS_URL . 'components/minishop2/img/mgr/extensions/other.png';
            } else {
                $row['thumbnail'] = $this->miniShop2->config['defaultThumb'];
            }
        }

        $row['properties'] = strpos($row['properties'], '{') === 0
            ? json_decode($row['properties'], true)
            : [];

        $row['actions'] = [];

        $row['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('ms2_gallery_file_update'),
            'action' => 'updateFile',
            'button' => false,
            'menu' => true,
        ];

        $row['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-share',
            'title' => $this->modx->lexicon('ms2_gallery_file_show'),
            'action' => 'showFile',
            'button' => false,
            'menu' => true,
        ];

        if ($row['type'] == 'image') {
            $row['actions'][] = [
                'cls' => '',
                'icon' => 'icon icon-refresh',
                'title' => $this->modx->lexicon('ms2_gallery_file_generate_thumbs'),
                'multiple' => $this->modx->lexicon('ms2_gallery_file_generate_thumbs'),
                'action' => 'generateThumbs',
                'button' => false,
                'menu' => true,
            ];
        }

        $row['actions'][] = [
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('ms2_gallery_file_delete'),
            'multiple' => $this->modx->lexicon('ms2_gallery_file_delete_multiple'),
            'action' => 'deleteFiles',
            'button' => false,
            'menu' => true,
        ];

        return $row;
    }
}

return 'msProductFileGetListProcessor';
