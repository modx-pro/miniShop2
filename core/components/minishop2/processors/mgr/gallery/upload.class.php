<?php

class msProductFileUploadProcessor extends modObjectProcessor {
	private $pid = 0;
	/* @var msProduct $product */
	private $product = 0;
	public $languageTopics = array('minishop2:default','minishop2:product');
	/* @var modMediaSource $mediaSource */
	public $mediaSource;

	public function initialize() {
		/* @var msProduct $product */
		if (!$product = $this->modx->getObject('msProduct', $_GET['id'])) {
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
			return $this->failure($this->modx->lexicon('ms2_err_gallery_ns'));
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
			return $this->failure($this->modx->lexicon('ms2_err_gallery_ext'));
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
			,'rank' => $this->modx->getCount('msProductFile', array('parent' => 0, 'product_id' => $this->pid))
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->modx->user->id
			,'active' => 1
		));

		$this->mediaSource->createContainer($product_file->get('path'), '/');
		$file = $this->mediaSource->createObject(
			$product_file->get('path')
			,$product_file->get('file')
			,$data['stream']
		);

		if ($file) {
			$product_file->set('url', $this->mediaSource->getObjectUrl($product_file->get('path').$product_file->get('file')));
			$product_file->save();
			$generate = $product_file->generateThumbnails($this->mediaSource);
			$thumb = $this->product->updateProductImage();
			if ($generate !== true) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not generate thumbnails for image with id = '.$product_file->get('id').'. '.$generate);
				return $this->failure($this->modx->lexicon('ms2_err_gallery_thumb'));
			}
		}
		else {
			return $this->failure($this->modx->lexicon('ms2_err_gallery_save') . ': '.print_r($this->mediaSource->getErrors(), 1));
		}

		return $this->success($thumb);
	}


	public function handleFile() {
		$stream = $name = $size = null;
		if (!empty($_SERVER['HTTP_X_FILE_NAME'])) {
			$name = $_SERVER['HTTP_X_FILE_NAME'];
			$stream = file_get_contents('php://input');
		}
		else if (!empty($_FILES['Filedata']) && @is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			$name = $_FILES['Filedata']['name'];
			$stream = file_get_contents($_FILES['Filedata']['tmp_name']);
		}

		if (!empty($stream)) {
			return array(
				'name' => $name
				,'stream' => $stream
				,'size' => $_SERVER['HTTP_CONTENT_LENGTH']
			);
		}
		else {
			return false;
		}
	}

}

return 'msProductFileUploadProcessor';