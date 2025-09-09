<?php

class msProductFileUploadProcessor extends modObjectProcessor
{
    public $classKey = 'msProductFile';
    public $languageTopics = ['minishop2:default', 'minishop2:product'];
    public $permission = 'msproductfile_save';
    /** @var modMediaSource $mediaSource */
    public $mediaSource;
    /** @var miniShop2 $miniShop2 */
    protected $miniShop2;
    /** @var msProduct $product */
    private $product = 0;

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }
        /** @var msProduct $product */
        $id = (int)$this->getProperty('id', @$_GET['id']);
        if (!$this->product = $this->modx->getObject('msProduct', $id)) {
            return $this->modx->lexicon('ms2_gallery_err_no_product');
        }
        if (!$this->mediaSource = $this->product->initializeMediaSource()) {
            return $this->modx->lexicon('ms2_gallery_err_no_source');
        }
        $this->miniShop2 = $this->modx->getService('miniShop2');

        return true;
    }

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$data = $this->handleFile()) {
            return $this->failure($this->modx->lexicon('ms2_err_gallery_ns'));
        }

        $properties = $this->mediaSource->getPropertyList();
        $pathinfo = $this->miniShop2->pathinfo($data['name']);
        $extension = strtolower($pathinfo['extension']);
        $filename = strtolower($pathinfo['filename']);

        if (!$extension) {
            $mime_type = mime_content_type($data['tmp_name']);
            $extension = $this->mime2ext($mime_type);
        }

        $image_extensions = $allowed_extensions = [];
        if (!empty($properties['imageExtensions'])) {
            $image_extensions = array_map('trim', explode(',', strtolower($properties['imageExtensions'])));
        }
        if (!empty($properties['allowedFileTypes'])) {
            $allowed_extensions = array_map('trim', explode(',', strtolower($properties['allowedFileTypes'])));
        }
        if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
            @unlink($data['tmp_name']);

            return $this->failure($this->modx->lexicon('ms2_err_gallery_ext'));
        }
        if (in_array($extension, $image_extensions)) {
            if (empty($data['properties']['height']) || empty($data['properties']['width'])) {
                @unlink($data['tmp_name']);

                return $this->failure($this->modx->lexicon('ms2_err_wrong_image'));
            }
            $type = 'image';
        } else {
            $type = $extension;
        }

        // Duplicate check
        $count = $this->modx->getCount($this->classKey, [
            'product_id' => $this->product->id,
            'hash' => $data['hash'],
            'parent' => 0,
        ]);
        if ($count) {
            @unlink($data['tmp_name']);

            return $this->failure($this->modx->lexicon('ms2_err_gallery_exists'));
        }

        $filename = !empty($properties['imageNameType']) && $properties['imageNameType'] == 'friendly'
            ? $this->product->cleanAlias($filename)
            : $data['hash'];
        $filename = str_replace(',', '', $filename) . '.' . $extension;
        $tmp_filename = $filename;
        $i = 1;
        while (true) {
            $count = $this->modx->getCount($this->classKey, [
                'product_id' => $this->product->id,
                'file' => $tmp_filename,
                'parent' => 0,
            ]);
            if (!$count) {
                $filename = $tmp_filename;
                break;
            } else {
                $pcre = '#(-' . ($i - 1) . '|)\.' . $extension . '$#';
                $tmp_filename = preg_replace($pcre, "-$i.$extension", $tmp_filename);
                $i++;
            }
        }

        $rank = isset($properties['imageUploadDir']) && empty($properties['imageUploadDir'])
            ? 0
            : $this->modx->getCount($this->classKey, ['parent' => 0, 'product_id' => $this->product->id]);

        /** @var msProductFile $uploaded_file */
        $uploaded_file = $this->modx->newObject($this->classKey, [
            'product_id' => $this->product->id,
            'parent' => 0,
            'name' => preg_replace('#\.' . $extension . '$#i', '', $data['name']),
            'file' => $filename,
            'path' => $this->product->id . '/',
            'source' => $this->mediaSource->get('id'),
            'type' => $type,
            'rank' => $rank,
            'createdon' => date('Y-m-d H:i:s'),
            'createdby' => $this->modx->user->id,
            'hash' => $data['hash'],
            'properties' => $data['properties'],
            'description' => $this->getProperty('description'),
        ]);

        $this->mediaSource->createContainer($uploaded_file->get('path'), '/');
        $this->mediaSource->errors = [];
        if ($this->mediaSource instanceof modFileMediaSource) {
            $upload = $this->mediaSource->createObject(
                $uploaded_file->get('path'),
                $uploaded_file->get('file'),
                file_get_contents($data['tmp_name'])
            );
        } else {
            $data['name'] = $filename;
            $upload = $this->mediaSource->uploadObjectsToContainer($uploaded_file->get('path'), [$data]);
        }
        @unlink($data['tmp_name']);

        if (!$upload) {
            return $this->failure(
                $this->modx->lexicon('ms2_err_gallery_save') . ': ' .
                    print_r($this->mediaSource->getErrors(), true)
            );
        }
        $url = $this->mediaSource->getObjectUrl($uploaded_file->get('path') . $uploaded_file->get('file'));
        $uploaded_file->set('url', $url);
        $uploaded_file->save();

        if (empty($rank)) {
            $imagesTable = $this->modx->getTableName($this->classKey);
            $sql = "UPDATE {$imagesTable} SET `rank` = `rank` + 1 WHERE product_id ='" . $this->product->id . "' AND id !='" . $uploaded_file->get(
                'id'
            ) . "'";
            $this->modx->exec($sql);
        }

        $generate = $uploaded_file->generateThumbnails($this->mediaSource);
        if ($generate !== true) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                '[miniShop2] Could not generate thumbnails for image with id = ' . $uploaded_file->get('id') .
                    '. ' . $generate
            );

            return $this->failure($this->modx->lexicon('ms2_err_gallery_thumb'));
        }
        $this->product->updateProductImage();
        return $this->success('', $uploaded_file);
    }

    /**
     * @return array|bool
     */
    public function handleFile()
    {
        $tf = tempnam(MODX_BASE_PATH, 'ms_');

        if (!empty($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $name = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $tf);
        } else {
            $file = $this->getProperty('file');
            if (!empty($file) && (strpos($file, '://') !== false || file_exists($file))) {
                $tmp = explode('/', $file);
                $name = end($tmp);
                if ($stream = fopen($file, 'r')) {
                    if ($res = fopen($tf, 'w')) {
                        while (!feof($stream)) {
                            fwrite($res, fread($stream, 80000));
                        }
                        fclose($res);
                    }
                    fclose($stream);
                }
            }
        }

        clearstatcache(true, $tf);
        if (file_exists($tf) && !empty($name) && $size = filesize($tf)) {
            $hash = ($o = $this->modx->newObject($this->classKey)) ? $o->generateHash($tf) : '';
            $data = [
                'name' => $name,
                'tmp_name' => $tf,
                'hash' => $hash,
                'properties' => [
                    'size' => $size,
                ],
            ];

            $tmp = getimagesize($tf);

            if (is_array($tmp)) {
                $data['properties'] = array_merge(
                    $data['properties'],
                    [
                        'width' => $tmp[0],
                        'height' => $tmp[1],
                        'bits' => $tmp['bits'],
                        'mime' => $tmp['mime'],
                    ]
                );
            } elseif (strpos($data['name'], '.webp') !== false) {
                $img = imagecreatefromwebp($tf);
                $width = imagesx($img);
                $height = imagesy($img);

                $data['properties'] = array_merge(
                    $data['properties'],
                    [
                        'width' => $width,
                        'height' => $height,
                        'mime' => 'image/webp',
                    ]
                );
            }
            return $data;
        }
        unlink($tf);
        return false;
    }


    /**
     * Get the file extension for a given mime type.
     *
     * @param string $mime The mime type to get the extension for.
     * @return string The file extension, or false if not found.
     */
    public function mime2ext($mime)
    {
        $mime_map = [
            'video/3gpp2'                                                               => '3g2',
            'video/3gp'                                                                 => '3gp',
            'video/3gpp'                                                                => '3gp',
            'application/x-compressed'                                                  => '7zip',
            'audio/x-acc'                                                               => 'aac',
            'audio/ac3'                                                                 => 'ac3',
            'application/postscript'                                                    => 'ai',
            'audio/x-aiff'                                                              => 'aif',
            'audio/aiff'                                                                => 'aif',
            'audio/x-au'                                                                => 'au',
            'video/x-msvideo'                                                           => 'avi',
            'video/msvideo'                                                             => 'avi',
            'video/avi'                                                                 => 'avi',
            'application/x-troff-msvideo'                                               => 'avi',
            'application/macbinary'                                                     => 'bin',
            'application/mac-binary'                                                    => 'bin',
            'application/x-binary'                                                      => 'bin',
            'application/x-macbinary'                                                   => 'bin',
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'application/bmp'                                                           => 'bmp',
            'application/x-bmp'                                                         => 'bmp',
            'application/x-win-bitmap'                                                  => 'bmp',
            'application/cdr'                                                           => 'cdr',
            'application/coreldraw'                                                     => 'cdr',
            'application/x-cdr'                                                         => 'cdr',
            'application/x-coreldraw'                                                   => 'cdr',
            'image/cdr'                                                                 => 'cdr',
            'image/x-cdr'                                                               => 'cdr',
            'zz-application/zz-winassoc-cdr'                                            => 'cdr',
            'application/mac-compactpro'                                                => 'cpt',
            'application/pkix-crl'                                                      => 'crl',
            'application/pkcs-crl'                                                      => 'crl',
            'application/x-x509-ca-cert'                                                => 'crt',
            'application/pkix-cert'                                                     => 'crt',
            'text/css'                                                                  => 'css',
            'text/x-comma-separated-values'                                             => 'csv',
            'text/comma-separated-values'                                               => 'csv',
            'application/vnd.msexcel'                                                   => 'csv',
            'application/x-director'                                                    => 'dcr',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'application/x-dvi'                                                         => 'dvi',
            'message/rfc822'                                                            => 'eml',
            'application/x-msdownload'                                                  => 'exe',
            'video/x-f4v'                                                               => 'f4v',
            'audio/x-flac'                                                              => 'flac',
            'video/x-flv'                                                               => 'flv',
            'image/gif'                                                                 => 'gif',
            'application/gpg-keys'                                                      => 'gpg',
            'application/x-gtar'                                                        => 'gtar',
            'application/x-gzip'                                                        => 'gzip',
            'application/mac-binhex40'                                                  => 'hqx',
            'application/mac-binhex'                                                    => 'hqx',
            'application/x-binhex40'                                                    => 'hqx',
            'application/x-mac-binhex40'                                                => 'hqx',
            'text/html'                                                                 => 'html',
            'image/x-icon'                                                              => 'ico',
            'image/x-ico'                                                               => 'ico',
            'image/vnd.microsoft.icon'                                                  => 'ico',
            'text/calendar'                                                             => 'ics',
            'application/java-archive'                                                  => 'jar',
            'application/x-java-application'                                            => 'jar',
            'application/x-jar'                                                         => 'jar',
            'image/jp2'                                                                 => 'jp2',
            'video/mj2'                                                                 => 'jp2',
            'image/jpx'                                                                 => 'jp2',
            'image/jpm'                                                                 => 'jp2',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'application/x-javascript'                                                  => 'js',
            'application/json'                                                          => 'json',
            'text/json'                                                                 => 'json',
            'application/vnd.google-earth.kml+xml'                                      => 'kml',
            'application/vnd.google-earth.kmz'                                          => 'kmz',
            'text/x-log'                                                                => 'log',
            'audio/x-m4a'                                                               => 'm4a',
            'audio/mp4'                                                                 => 'm4a',
            'application/vnd.mpegurl'                                                   => 'm4u',
            'audio/midi'                                                                => 'mid',
            'application/vnd.mif'                                                       => 'mif',
            'video/quicktime'                                                           => 'mov',
            'video/x-sgi-movie'                                                         => 'movie',
            'audio/mpeg'                                                                => 'mp3',
            'audio/mpg'                                                                 => 'mp3',
            'audio/mpeg3'                                                               => 'mp3',
            'audio/mp3'                                                                 => 'mp3',
            'video/mp4'                                                                 => 'mp4',
            'video/mpeg'                                                                => 'mpeg',
            'application/oda'                                                           => 'oda',
            'audio/ogg'                                                                 => 'ogg',
            'video/ogg'                                                                 => 'ogg',
            'application/ogg'                                                           => 'ogg',
            'font/otf'                                                                  => 'otf',
            'application/x-pkcs10'                                                      => 'p10',
            'application/pkcs10'                                                        => 'p10',
            'application/x-pkcs12'                                                      => 'p12',
            'application/x-pkcs7-signature'                                             => 'p7a',
            'application/pkcs7-mime'                                                    => 'p7c',
            'application/x-pkcs7-mime'                                                  => 'p7c',
            'application/x-pkcs7-certreqresp'                                           => 'p7r',
            'application/pkcs7-signature'                                               => 'p7s',
            'application/pdf'                                                           => 'pdf',
            'application/octet-stream'                                                  => 'pdf',
            'application/x-x509-user-cert'                                              => 'pem',
            'application/x-pem-file'                                                    => 'pem',
            'application/pgp'                                                           => 'pgp',
            'application/x-httpd-php'                                                   => 'php',
            'application/php'                                                           => 'php',
            'application/x-php'                                                         => 'php',
            'text/php'                                                                  => 'php',
            'text/x-php'                                                                => 'php',
            'application/x-httpd-php-source'                                            => 'php',
            'image/png'                                                                 => 'png',
            'image/x-png'                                                               => 'png',
            'application/powerpoint'                                                    => 'ppt',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.ms-office'                                                 => 'ppt',
            'application/msword'                                                        => 'doc',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-photoshop'                                                   => 'psd',
            'image/vnd.adobe.photoshop'                                                 => 'psd',
            'audio/x-realaudio'                                                         => 'ra',
            'audio/x-pn-realaudio'                                                      => 'ram',
            'application/x-rar'                                                         => 'rar',
            'application/rar'                                                           => 'rar',
            'application/x-rar-compressed'                                              => 'rar',
            'audio/x-pn-realaudio-plugin'                                               => 'rpm',
            'application/x-pkcs7'                                                       => 'rsa',
            'text/rtf'                                                                  => 'rtf',
            'text/richtext'                                                             => 'rtx',
            'video/vnd.rn-realvideo'                                                    => 'rv',
            'application/x-stuffit'                                                     => 'sit',
            'application/smil'                                                          => 'smil',
            'text/srt'                                                                  => 'srt',
            'image/svg+xml'                                                             => 'svg',
            'application/x-shockwave-flash'                                             => 'swf',
            'application/x-tar'                                                         => 'tar',
            'application/x-gzip-compressed'                                             => 'tgz',
            'image/tiff'                                                                => 'tiff',
            'font/ttf'                                                                  => 'ttf',
            'text/plain'                                                                => 'txt',
            'text/x-vcard'                                                              => 'vcf',
            'application/videolan'                                                      => 'vlc',
            'text/vtt'                                                                  => 'vtt',
            'audio/x-wav'                                                               => 'wav',
            'audio/wave'                                                                => 'wav',
            'audio/wav'                                                                 => 'wav',
            'application/wbxml'                                                         => 'wbxml',
            'video/webm'                                                                => 'webm',
            'image/webp'                                                                => 'webp',
            'audio/x-ms-wma'                                                            => 'wma',
            'application/wmlc'                                                          => 'wmlc',
            'video/x-ms-wmv'                                                            => 'wmv',
            'video/x-ms-asf'                                                            => 'wmv',
            'font/woff'                                                                 => 'woff',
            'font/woff2'                                                                => 'woff2',
            'application/xhtml+xml'                                                     => 'xhtml',
            'application/excel'                                                         => 'xl',
            'application/msexcel'                                                       => 'xls',
            'application/x-msexcel'                                                     => 'xls',
            'application/x-ms-excel'                                                    => 'xls',
            'application/x-excel'                                                       => 'xls',
            'application/x-dos_ms_excel'                                                => 'xls',
            'application/xls'                                                           => 'xls',
            'application/x-xls'                                                         => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-excel'                                                  => 'xlsx',
            'application/xml'                                                           => 'xml',
            'text/xml'                                                                  => 'xml',
            'text/xsl'                                                                  => 'xsl',
            'application/xspf+xml'                                                      => 'xspf',
            'application/x-compress'                                                    => 'z',
            'application/x-zip'                                                         => 'zip',
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/s-compressed'                                                  => 'zip',
            'multipart/x-zip'                                                           => 'zip',
            'text/x-scriptzsh'                                                          => 'zsh',
        ];

        return isset($mime_map[$mime]) ? $mime_map[$mime] : false;
    }
}

return 'msProductFileUploadProcessor';
