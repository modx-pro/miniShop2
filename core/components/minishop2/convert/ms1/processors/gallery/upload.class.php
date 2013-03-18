<?php

require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/gallery/upload.class.php';

class msImportProductFileUploadProcessor extends msProductFileUploadProcessor {
	private $pid = 0;
	/* @var msProduct $product */
	private $product = 0;
	public $languageTopics = array('minishop2:default','minishop2:product');
	/* @var modMediaSource $mediaSource */
	public $mediaSource;


	public function initialize() {
		/* @var msProduct $product */
		if (!$product = $this->modx->getObject('msProduct', array('id' => $this->getProperty('id'), 'class_key' => 'msProduct'))) {
			return $this->modx->lexicon('ms2_gallery_err_no_product');
		}
		if (!$this->mediaSource = $product->initializeMediaSource()) {
			return $this->modx->lexicon('ms2_gallery_err_no_source');
		}

		$this->pid = $product->get('id');
		$this->product = $product;
		return true;
	}


	public function process() {
		if (!$data = $this->handleFile ()) {
			return $this->failure($this->modx->lexicon('ms2_gallery_err_no_file'));
		}

		$properties = $this->mediaSource->get('properties');
		$tmp = explode('.',$data['name']);
		$extension = strtolower(end($tmp));

		$image_extensions = $allowed_extensions = array();
		if (!empty($properties['imageExtensions']['value'])) {
			$image_extensions = array_map('trim', explode(',',strtolower($properties['imageExtensions']['value'])));
		}
		if (!empty($properties['allowedFileTypes']['value'])) {
			$allowed_extensions = array_map('trim', explode(',',strtolower($properties['allowedFileTypes']['value'])));
		}

		if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
			return $this->failure($this->modx->lexicon('ms2_product_gallery_err_wrong_ext'));
		}
		else if (in_array($extension, $image_extensions)) {$type = 'image';}
		else {$type = $extension;}

		/* @var msProductFile $product_file */
		$product_file = $this->modx->newObject('msProductFile', array(
			'product_id' => $this->pid
			,'parent' => 0
			,'name' => $data['name']
			,'file' => md5($data['name'] . time() . $this->pid) . '.' . $extension
			,'path' => $this->pid.'/'
			,'source' => $this->mediaSource->get('id')
			,'type' => $type
			,'rank' => $this->getProperty('rank', $this->modx->getCount('msProductFile', array('parent' => 0, 'product_id' => $this->pid)))
			,'description' => $this->getProperty('description')
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->modx->user->id
			,'active' => 1
		));

		$dir = $this->mediaSource->createContainer($product_file->get('path'), '/');
		$file = $this->mediaSource->createObject(
			$product_file->get('path')
			,$product_file->get('file')
			,$data['stream']
		);

		if ($file) {
			$product_file->set('url', $this->mediaSource->getObjectUrl($product_file->get('path').$product_file->get('file')));
			$product_file->save();
			$generate = $product_file->generateThumbnails($this->mediaSource);
			$this->product->updateProductImage();
			if ($generate !== true) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not generate thumbnails for image with id = '.$product_file->get('id').'. '.$generate);
			}
		}

		return $this->success($product_file->toArray());
	}


	public function handleFile() {
		$name = $this->getProperty('name');
		$stream = file_get_contents($this->getProperty('file'));
		$size = filesize($this->getProperty('file'));

		if (!empty($stream) && !empty($size)) {
			return array(
				'name' => $name
				,'stream' => $stream
				,'size' => $size
			);
		}
		else {
			return false;
		}
	}

}

return 'msImportProductFileUploadProcessor';