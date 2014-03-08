<?php

class msProductFileUploadProcessor extends modObjectProcessor {
	public $classKey = 'msProductFile';
	public $ObjectKey = 'msProductFile';
	public $languageTopics = array('minishop2:default','minishop2:product');
	public $permission = 'msproductfile_save';
	/* @var modMediaSource $mediaSource */
	public $mediaSource;
	/* @var msProduct $product */
	private $product = 0;


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		/* @var msProduct $product */
		$id = $this->getProperty('id', @$_GET['id']);
		if (!$product = $this->modx->getObject('msProduct', $id)) {
			return $this->modx->lexicon('ms2_gallery_err_no_product');
		}
		if (!$this->mediaSource = $product->initializeMediaSource()) {
			return $this->modx->lexicon('ms2_gallery_err_no_source');
		}

		$this->product = $product;
		return true;
	}


	/** {@inheritDoc} */
	public function process() {
		if (!$data = $this->handleFile ()) {
			return $this->failure($this->modx->lexicon('ms2_err_gallery_ns'));
		}

		$properties = $this->mediaSource->getProperties();
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
		$hash = sha1($data['stream']);

		if ($this->modx->getCount('msProductFile', array('product_id' => $this->product->id, 'hash' => $hash, 'parent' => 0))) {
			return $this->failure($this->modx->lexicon('ms2_err_gallery_exists'));
		}

		$filename = !empty($properties['imageNameType']) && $properties['imageNameType']['value'] == 'friendly'
			? $this->product->cleanAlias($data['name'])
			: $hash . '.' . $extension;
		if (strpos($filename, '.'.$extension) === false) {
			$filename .= '.'.$extension;
		}

		/* @var msProductFile $product_file */
		$product_file = $this->modx->newObject('msProductFile', array(
			'product_id' => $this->product->id,
			'parent' => 0,
			'name' => $data['name'],
			'file' => $filename,
			'path' => $this->product->id.'/',
			'source' => $this->mediaSource->get('id'),
			'type' => $type,
			'rank' => $this->modx->getCount('msProductFile', array('parent' => 0, 'product_id' => $this->product->id)),
			'createdon' => date('Y-m-d H:i:s'),
			'createdby' => $this->modx->user->id,
			'active' => 1,
			'hash' => $hash,
			'properties' => $data['properties'],
		));

		$this->mediaSource->createContainer($product_file->path, '/');
		unset($this->mediaSource->errors['file']);
		$file = $this->mediaSource->createObject(
			$product_file->get('path')
			,$product_file->get('file')
			,$data['stream']
		);

		if ($file) {
			$url = $this->mediaSource->getObjectUrl($product_file->get('path').$product_file->get('file'));
			$product_file->set('url', $url);
			$product_file->save();
			$generate = $product_file->generateThumbnails($this->mediaSource);
			if ($generate !== true) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not generate thumbnails for image with id = '.$product_file->get('id').'. '.$generate);
				return $this->failure($this->modx->lexicon('ms2_err_gallery_thumb'));
			}
			else {
				$this->product->updateProductImage();
				return $this->success($url);
			}
		}
		else {
			return $this->failure($this->modx->lexicon('ms2_err_gallery_save') . ': '.print_r($this->mediaSource->getErrors(), 1));
		}
	}


	/**
	 * @return array|bool
	 */
	public function handleFile() {
		$stream = $name = null;

		$contentType = isset($_SERVER["HTTP_CONTENT_TYPE"]) ? $_SERVER["HTTP_CONTENT_TYPE"] : $_SERVER["CONTENT_TYPE"];

		$file = $this->getProperty('file');
		if (!empty($file) && file_exists($file)) {
			$tmp = explode('/', $file);
			$name = end($tmp);
			$stream = file_get_contents($file);
		}
		elseif (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				$name = $_FILES['file']['name'];
				$stream = file_get_contents($_FILES['file']['tmp_name']);
			}
		}
		else {
			$name = $this->getProperty('name', @$_REQUEST['name']);
			$stream = file_get_contents('php://input');
		}

		if (!empty($stream)) {
			$data = array(
				'name' => $name,
				'stream' => $stream,
				'properties' => array(
					'size' => strlen($stream),
				)
			);

			$tf = tempnam(MODX_BASE_PATH, 'ms_');
			file_put_contents($tf, $stream);
			$tmp = getimagesize($tf);
			if (is_array($tmp)) {
				$data['properties'] = array_merge($data['properties'],
					array(
						'width' => $tmp[0],
						'height' => $tmp[1],
						'bits' => $tmp['bits'],
						'mime' => $tmp['mime'],
					)
				);
			}
			unlink($tf);
			return $data;
		}
		else {
			return false;
		}
	}

}

return 'msProductFileUploadProcessor';