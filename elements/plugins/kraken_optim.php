<?php
/**
 * @name Kraken
 * @description
 * @PluginEvents OnFileManagerFileUpdate
 */

// Your core_path will change depending on whether your code is running on your development environment
// or on a production environment (deployed via a Transport Package).  Make sure you follow the pattern
// outlined here. See https://github.com/craftsmancoding/repoman/wiki/Conventions for more info
$core_path = $modx->getOption('kraken.core_path', null, MODX_CORE_PATH.'components/kraken/');
include_once $core_path .'vendor/autoload.php';

$api = $modx->getOption('kraken.api_key');
$api_secret = $modx->getOption('kraken.api_secret');

$kraken = new Kraken($api, $api_secret);

switch ($modx->event->name) {

    case 'OnFileManagerFileUpdate':

		// get the file
		$file = $modx->event->params['files']['file']; 
		// abort on error
		if ($file['error']  !=  0) {
		    $modx->log(modX::LOG_LEVEL_ERROR, 'Kraken: $file["error"] occured.');
		    return;
		}
		// get upload directory from OnFileManagerUpload event
		$directory = $modx->event->params['directory']; 
		// get filename
		$fileName = $file['name'];
		// get id of default_media_source
		$defaultMediaSource = $modx->getOption('default_media_source');
		// get modMediaSource object using default_media_source id
		$mediaSource = $modx->getObject('modMediaSource', array('id' => $defaultMediaSource ));
		// get modMediaSource properties
		$mediaSourceProps = $mediaSource->get('properties');
		$mediaSourceBasePath = $mediaSourceProps['basePath']['value'];
		// create Full-size master image URL
		$sourceImageUrl = MODX_SITE_URL . $mediaSourceBasePath . $directory . $fileName;
		// create target image path
		$targetImagePath = MODX_BASE_PATH . $mediaSourceBasePath . $directory . $fileName;

    	// Getting the file path
		$params = array(
		    "url" => $sourceImageUrl,
		    "wait" => true,
		    "lossy" => true
		);       

        // Uploading the compressed file
    	
    	$data = $kraken->url($params);
		
		if ($data["success"]) {
			file_put_contents($targetImagePath, $data["kraked_url"]);
		    $modx->log(modx::LOG_LEVEL_INFO, 'Success. Optimized image URL: ' . $data["kraked_url"]);
		} else {
		    $modx->log(modx::LOG_LEVEL_ERROR, 'Fail. Error message: ' . $data["message"]);
		}

        break;
}