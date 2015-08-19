<?php

/*---------------------------------*/
if (!function_exists('installPackage')) {
	function installPackage($packageName) {
		global $modx;

		/* @var modTransportProvider $provider */
		if (!$provider = $modx->getObject('transport.modTransportProvider', array('service_url:LIKE' => '%simpledream.ru%', 'OR:service_url:LIKE' => '%modstore.pro%'))) {
			$provider = $modx->getObject('transport.modTransportProvider', 1);
		}

		$modx->getVersionData();
		$productVersion = $modx->version['code_name'] . '-' . $modx->version['full_version'];

		$response = $provider->request('package', 'GET', array(
			'supports' => $productVersion,
			'query' => $packageName
		));

		if (!empty($response)) {
			$foundPackages = simplexml_load_string($response->response);
			foreach ($foundPackages as $foundPackage) {
				/* @var modTransportPackage $foundPackage */
				if ($foundPackage->name == $packageName) {
					$sig = explode('-', $foundPackage->signature);
					$versionSignature = explode('.', $sig[1]);
					$url = $foundPackage->location;

					if (!downloadPackage($url, $modx->getOption('core_path') . 'packages/' . $foundPackage->signature . '.transport.zip')) {
						return array(
							'success' => 0,
							'message' => "Could not download package <b>{$packageName}</b>.",
						);
					}

					/* add in the package as an object so it can be upgraded */
					/** @var modTransportPackage $package */
					$package = $modx->newObject('transport.modTransportPackage');
					$package->set('signature', $foundPackage->signature);
					$package->fromArray(array(
						'created' => date('Y-m-d h:i:s'),
						'updated' => null,
						'state' => 1,
						'workspace' => 1,
						'provider' => $provider->id,
						'source' => $foundPackage->signature . '.transport.zip',
						'package_name' => $packageName,
						'version_major' => $versionSignature[0],
						'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
						'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
					));

					if (!empty($sig[2])) {
						$r = preg_split('/([0-9]+)/', $sig[2], -1, PREG_SPLIT_DELIM_CAPTURE);
						if (is_array($r) && !empty($r)) {
							$package->set('release', $r[0]);
							$package->set('release_index', (isset($r[1]) ? $r[1] : '0'));
						}
						else {
							$package->set('release', $sig[2]);
						}
					}

					if ($package->save() && $package->install()) {
						return array(
							'success' => 1,
							'message' => "<b>{$packageName}</b> was successfully installed",
						);
					}
					else {
						return array(
							'success' => 0,
							'message' => "Could not save package <b>{$packageName}</b>",
						);
					}
					break;
				}
			}
		}
		else {
			return array(
				'success' => 0,
				'message' => "Could not find <b>{$packageName}</b> in MODX repository",
			);
		}
		return true;
	}
}

if (!function_exists('downloadPackage')) {
	function downloadPackage($src, $dst) {
		if (ini_get('allow_url_fopen')) {
			$file = @file_get_contents($src);
		}
		else {
			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $src);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 180);
				$safeMode = @ini_get('safe_mode');
				$openBasedir = @ini_get('open_basedir');
				if (empty($safeMode) && empty($openBasedir)) {
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				}

				$file = curl_exec($ch);
				curl_close($ch);
			}
			else {
				return false;
			}
		}
		file_put_contents($dst, $file);

		return file_exists($dst);
	}
}


$success = false;
switch (@$options[xPDOTransport::PACKAGE_ACTION]) {
	case xPDOTransport::ACTION_INSTALL:
	case xPDOTransport::ACTION_UPGRADE:
		/* @var modX $modx */
		$modx = &$object->xpdo;
		/* Checking and installing required packages */
		$packages = array(
			'pdoTools' => '2.1.0-pl',
		);

		foreach ($packages as $package_name => $version) {
			$installed = $modx->getIterator('transport.modTransportPackage', array('package_name' => $package_name));
			/** @var modTransportPackage $package */
			foreach ($installed as $package) {
				if ($package->compareVersion($version, '<=')) {
					continue(2);
				}
			}
			$modx->log(modX::LOG_LEVEL_INFO, "Trying to install <b>{$package_name}</b>. Please wait...");
			$response = installPackage($package_name);
			$level = $response['success']
				? modX::LOG_LEVEL_INFO
				: modX::LOG_LEVEL_ERROR;
			$modx->log($level, $response['message']);
		}

		$success = true;
		break;

	case xPDOTransport::ACTION_UNINSTALL:
		$success = true;
		break;
}

return $success;