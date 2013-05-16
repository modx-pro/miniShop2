<?php
class msProductFile extends xPDOSimpleObject {
	public $file;
	/* @var modPhpThumb $phpThumb */
	public $phpThumb;
	/* @var modMediaSource $mediaSource */
	public $mediaSource;

	public function prepareSource(modMediaSource $mediaSource = null) {
		if ($mediaSource) {
			$this->mediaSource = $mediaSource;
		}
		else {
			/* @var msProduct $product */
			if ($product = $this->xpdo->getObject('msProduct', $this->get('product_id'))) {
				$this->mediaSource = $product->initializeMediaSource();
				if (!$this->mediaSource || !($this->mediaSource instanceof modMediaSource)) {
					return 'Could not initialize media source for product with id = '.$this->get('product_id');
				}
			}
			else {
				return 'Could not find product with id = '.$this->get('product_id');
			}
		}

		return true;
	}


	public function generateThumbnails(modMediaSource $mediaSource = null) {
		if ($this->get('type') != 'image' || $this->get('parent') != 0) {return true;}

		$prepare = $this->prepareSource($mediaSource);
		if ($prepare !== true) {return $prepare;}

		$this->file = $this->mediaSource->getObjectContents($this->get('path').$this->get('file'));
		if (!empty($this->mediaSource->errors['file'])) {
			return 'Could not retrieve file "'.$this->get('path').$this->get('file').'" from media source. '.$this->mediaSource->errors['file'];
		}

		require_once  MODX_CORE_PATH . 'model/phpthumb/modphpthumb.class.php';

		$properties = $this->mediaSource->get('properties');
		$thumbnails = array();
		if (array_key_exists('thumbnails', $properties) && !empty($properties['thumbnails']['value'])) {
			$thumbnails = $this->xpdo->fromJSON($properties['thumbnails']['value']);
		}

		if (empty($thumbnails)) {
			$thumbnails = array(array(
				'w' => 120
				,'h' => 90
				,'q' => 90
				,'zc' => 'T'
				,'bg' => '000000'
				,'f' => !empty($properties['thumbnailType']['value']) ? $properties['thumbnailType']['value'] : 'jpg'
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



	public function makeThumbnail($options = array()) {
		$phpThumb = new modPhpThumb($this->xpdo);
		$phpThumb->initialize();

		$tmp = tempnam(MODX_BASE_PATH, 'ms_');
		file_put_contents($tmp, $this->file['content']);
		$phpThumb->setSourceFilename($tmp);

		foreach ($options as $k => $v) {
			$phpThumb->setParameter($k, $v);
		}

		if ($phpThumb->GenerateThumbnail() && $phpThumb->RenderOutput()) {
			@unlink($phpThumb->sourceFilename);
			@unlink($tmp);
			return $phpThumb->outputImageData;
		}
		else {
			$this->xpdo->log(modX::LOG_LEVEL_ERROR, 'Could not generate thumbnail for "'.$this->get('url').'". '.print_r($phpThumb->debugmessages,1));
			return false;
		}
	}


	public function saveThumbnail($raw_image, $options = array()) {
		$tmp = explode('.', $this->get('file'));
		$file = $tmp[0] . '.' . $options['f'];
		$path = $this->get('path') . $options['w'] .'x'.$options['h'] .'/';

		/* @var msProductFile $product_file */
		$product_file = $this->xpdo->newObject('msProductFile', array(
			'product_id' => $this->get('product_id')
			,'parent' => $this->get('id')
			,'name' => $this->get('name')
			,'file' => $file
			,'path' => $path
			,'source' => $this->mediaSource->get('id')
			,'type' => $this->get('type')
			,'rank' => $this->get('rank')
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->xpdo->user->id
			,'active' => 1
		));

		$dir = $this->mediaSource->createContainer($product_file->get('path'), '/');
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


	public function getFirstThumbnail() {
		$c = array(
			'product_id' => $this->get('product_id')
			,'parent' => $this->get('id')
			,'path:LIKE' => '%'.$this->xpdo->getOption('ms2_product_thumbnail_size', null, '120x90').'/'
		);

		if (!$this->xpdo->getCount('msProductFile', $c)) {
			unset($c['path']);
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


	public function remove(array $ancestors= array ()) {
		$this->prepareSource();
		$this->mediaSource->removeObject($this->get('path').$this->get('file'));

		return parent::remove($ancestors);
	}

}