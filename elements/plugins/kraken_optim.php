<?php
/**
 * @name Kraken
 * @description Just add your api and secret keys and all images will get optimized on upload with the Kraken API
 * @author Manuel Barbiero
 * @PluginEvents OnFileManagerFileUpdate
 */

$core_path  = $modx->getOption('kraken.core_path', null, MODX_CORE_PATH . 'components/kraken/');
include_once $core_path . 'vendor/autoload.php';

// System settings
$kraken     = $modx->getOption('kraken.enable');        // Boolen Yes/No   - default: No
$api        = $modx->getOption('kraken.api_key');       // String
$api_secret = $modx->getOption('kraken.api_secret');    // String
$lossy		= $modx->getOption('kraken.lossy');         // Boolean Yes/No  - default: Yes
$quality	= $modx->getOption('kraken.lossy_quality'); // Number          - defualt: 75
$resize     = $modx->getOption('kraken.resize');        // Boolean Yes/No  - default: No
$width      = $modx->getOption('kraken.width');         // Number          - defualt: 1600
$height     = $modx->getOption('kraken.height');        // Number
$strategy   = $modx->getOption('kraken.strategy');      // String          - default: 'fit'

// Init Kraken Class
$kraken     = new Kraken($api, $api_secret);

switch ($modx->event->name) {
    
    case 'OnFileManagerFileUpdate':
        
        // Get the file
        $file = $modx->event->params['files']['file'];
        // Abort on error
        if ($file['error'] != 0) {
            $modx->log(modX::LOG_LEVEL_ERROR, 'Kraken: '.$file["error"].' occured.');
            return;
        }
        // Get upload directory from OnFileManagerUpload event
        $directory           = $modx->event->params['directory'];
        // Get filename
        $fileName            = $file['name'];
        // Get id of default_media_source
        $defaultMediaSource  = $modx->getOption('default_media_source');
        // Get modMediaSource object using default_media_source id
        $mediaSource         = $modx->getObject('modMediaSource', array(
            'id' => $defaultMediaSource
        ));
        // Get modMediaSource properties
        $mediaSourceProps    = $mediaSource->get('properties');
        $mediaSourceBasePath = $mediaSourceProps['basePath']['value'];
        // Get Full-size master image URL
        $sourceImageUrl      = MODX_SITE_URL . $mediaSourceBasePath . $directory . $fileName;
        // Get target image path
        $targetImagePath     = MODX_BASE_PATH . $mediaSourceBasePath . $directory . $fileName;
		
		//Check if file is an actual image 
		$isImage = getimagesize($imagePath) ? true : false;
		if (!$isImage) {
			$modx->log(modX::LOG_LEVEL_ERROR, 'Kraken: '.$file["name"].' is not an image.');
			return;
		}

        //Check if Kraken is enabled through system settings
        if(!$krakenEnabled) return;

        // Setting compression params
        $params = array(
            "url" => $sourceImageUrl,
			"wait" => true,
			"resize" => ($resize) ? array( 
				"width" => $width,
				"height" => $height,
				"strategy" => $strategy
			): false,
			"lossy" => boolval($lossy),
			"quality" => $quality
        );
        
        // Uploading the compressed file
        $data = $kraken->url($params);
        
        if ($data["success"]) {
            file_put_contents($targetImagePath, $data["kraked_url"]);
            $modx->log(modx::LOG_LEVEL_INFO, 'Kraken: Success. Optimized image URL: ' . $data["kraked_url"]);
        } else {
            $modx->log(modx::LOG_LEVEL_ERROR, 'Kraken: Fail. Error message: ' . $data["message"]);
        }
        
        break;
}