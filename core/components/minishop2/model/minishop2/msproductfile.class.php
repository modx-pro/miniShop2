<?php
class msProductFile extends xPDOSimpleObject {
	public $file;
	/* @var modPhpThumb $phpThumb */
	public $phpThumb;
	/* @var modMediaSource $mediaSource */
	public $mediaSource;


	/**
	 * @param modMediaSource $mediaSource
	 *
	 * @return bool|string
	 */
	public function prepareSource(modMediaSource $mediaSource = null) {
		if (is_object($this->mediaSource) && $this->mediaSource instanceof modMediaSource) {
			return true;
		}
		elseif ($mediaSource) {
			$this->mediaSource = $mediaSource;
			return true;
		}
		else {
			/* @var msProduct $product */
			if ($product = $this->xpdo->getObject('msProduct', $this->get('product_id'))) {
				$this->mediaSource = $product->initializeMediaSource();
				if (!$this->mediaSource || !($this->mediaSource instanceof modMediaSource)) {
					return 'Could not initialize media source for product with id = '.$this->get('product_id');
				}
				return true;
			}
			else {
				return 'Could not find product with id = '.$this->get('product_id');
			}
		}
	}


	/**
	 * @param modMediaSource $mediaSource
	 *
	 * @return bool|string
	 */
	public function generateThumbnails(modMediaSource $mediaSource = null) {
		if ($this->get('type') != 'image' || $this->get('parent') != 0) {return true;}

		$prepare = $this->prepareSource($mediaSource);
		if ($prepare !== true) {return $prepare;}

		$this->file = $this->mediaSource->getObjectContents($this->get('path').$this->get('file'));
		if (!empty($this->mediaSource->errors['file'])) {
			return 'Could not retrieve file "'.$this->get('path').$this->get('file').'" from media source. '.$this->mediaSource->errors['file'];
		}

		require_once  MODX_CORE_PATH . 'model/phpthumb/modphpthumb.class.php';
		$properties = $this->mediaSource->getProperties();
		$thumbnails = array();
		if (array_key_exists('thumbnails', $properties) && !empty($properties['thumbnails']['value'])) {
			$thumbnails = $this->xpdo->fromJSON($properties['thumbnails']['value']);
		}

		if (empty($thumbnails)) {
			$thumbnails = array(array(
				'w' => 120,
				'h' => 90,
				'q' => 90,
				'zc' => 'T',
				'bg' => '000000',
				'f' => !empty($properties['thumbnailType']['value']) ? $properties['thumbnailType']['value'] : 'jpg',
			));
		}

		foreach ($thumbnails as $options) {
			if (empty($options['f'])) {
				$options['f'] = !empty($properties['thumbnailType']['value']) ? $properties['thumbnailType']['value'] : 'jpg';
			}
			if ($image = $this->makeThumbnail($options)) {
				$this->saveThumbnail($image, $options);
			}
		}

		return true;
	}


	/**
	 * @param array $options
	 *
	 * @return bool|null
	 */
	public function makeThumbnail($options = array()) {
		$phpThumb = new modPhpThumb($this->xpdo);
		$phpThumb->initialize();

		$tf = tempnam(MODX_BASE_PATH, 'ms_');
		file_put_contents($tf, $this->file['content']);
		$phpThumb->setSourceFilename($tf);

		foreach ($options as $k => $v) {
			$phpThumb->setParameter($k, $v);
		}

		if ($phpThumb->GenerateThumbnail()) {
			ImageInterlace($phpThumb->gdimg_output, true);
			if ($phpThumb->RenderOutput()) {
				@unlink($phpThumb->sourceFilename);
				@unlink($tf);
				return $phpThumb->outputImageData;
			}
		}
		else {
			$this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Could not generate thumbnail for "'.$this->get('url').'". '.print_r($phpThumb->debugmessages,1));
		}
		return false;
	}


	/**
	 * @param $raw_image
	 * @param array $options
	 *
	 * @return bool
	 */
	public function saveThumbnail($raw_image, $options = array()) {
		$filename = pathinfo($this->get('file'),PATHINFO_FILENAME) . '.' . $options['f'];
		$path = $this->get('path') . $options['w'] .'x'.$options['h'] .'/';

		/* @var msProductFile $product_file */
		$product_file = $this->xpdo->newObject('msProductFile', array(
			'product_id' => $this->get('product_id'),
			'parent' => $this->get('id'),
			'name' => $this->get('name'),
			'file' => $filename,
			'path' => $path,
			'source' => $this->mediaSource->get('id'),
			'type' => $this->get('type'),
			'rank' => $this->get('rank'),
			'createdon' => date('Y-m-d H:i:s'),
			'createdby' => $this->xpdo->user->id,
			'active' => 1,
			'hash' => sha1($raw_image),
			'properties' => array(
				'size' => strlen($raw_image),
			)
		));

		$tf = tempnam(MODX_BASE_PATH, 'ms_');
		file_put_contents($tf, $raw_image);
		$tmp = getimagesize($tf);
		if (is_array($tmp)) {
			$product_file->set('properties', array_merge($product_file->get('properties'),
				array(
					'width' => $tmp[0],
					'height' => $tmp[1],
					'bits' => $tmp['bits'],
					'mime' => $tmp['mime'],
				)
			));
		}
		unlink($tf);

		$this->mediaSource->createContainer($product_file->get('path'), '/');
		unset($this->mediaSource->errors['file']);
		$file = $this->mediaSource->createObject(
			$product_file->get('path')
			,$product_file->get('file')
			,$raw_image
		);

		if ($file) {
			$product_file->set('url', $this->mediaSource->getObjectUrl($product_file->get('path').$product_file->get('file')));
			$product_file->save();
			return true;
		}
		else {
			return false;
		}
	}


	/**
	 * @return array|mixed
	 */
	public function getFirstThumbnail() {
		$c = array(
			'product_id' => $this->get('product_id')
			,'parent' => $this->get('id')
			,'path:LIKE' => '%'.$this->xpdo->getOption('ms2_product_thumbnail_size', null, '120x90', true).'/'
			,'type' => 'image'
		);

		if (!$this->xpdo->getCount('msProductFile', $c)) {
			unset($c['path:LIKE']);
		}

		$q = $this->xpdo->newQuery('msProductFile', $c);
		$q->limit(1);
		$q->sortby('url', 'ASC');
		$q->select('id,url');

		$res = array();
		if ($q->prepare() && $q->stmt->execute()) {
			$res = $q->stmt->fetch(PDO::FETCH_ASSOC);
		}

		return $res;
	}


	/**
	 * @param array $ancestors
	 *
	 * @return bool
	 */
	public function remove(array $ancestors= array ()) {
		$this->prepareSource();
		if (!$this->mediaSource->removeObject($this->get('path').$this->get('file'))) {
			$this->xpdo->log(xPDO::LOG_LEVEL_ERROR,
				'Could not remove file at "'.$this->get('path').$this->get('file').'": '.$this->mediaSource->errors['file']
			);
		}
		$children = $this->xpdo->getIterator('msProductFile', array('parent' => $this->get('id')));
		/** @var msProductFile $child */
		foreach ($children as $child) {
			$child->remove();
		}

		return parent::remove($ancestors);
	}


	/**
	 * Recursive file rename
	 *
	 * @param string $new_name
	 * @param string $old_name
	 *
	 * @return bool
	 */
	public function rename($new_name, $old_name = '') {
		if (empty($old_name)) {
			$old_name = $this->get('file');
		}

		$path = $this->get('path');
		$tmp = explode('.', $old_name);
		$extension = end($tmp);
		$file = preg_replace('/\..*$/', '', $new_name) . '.' . $extension;

		$this->prepareSource();
		if ($this->mediaSource->renameObject($path . $old_name, $file)) {
			$this->set('file', $file);
			$this->set('url', $this->mediaSource->getObjectUrl($path . $file));
			$this->save();
			// Processing children
			$children = $this->getMany('Children');
			if (!empty($children)) {
				/* @var msProductFile $child */
				foreach ($children as $child) {
					$child->rename($new_name, $child->get('file'));
				}
			}
			return true;
		}
		return false;
	}
}
