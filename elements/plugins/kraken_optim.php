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
$kraken     = (bool)    $modx->getOption('kraken.enable');        // Boolen Yes/No   - default: No
$api        = (string)  $modx->getOption('kraken.api_key');       // String
$api_secret = (string)  $modx->getOption('kraken.api_secret');    // String
$lossy		= (bool)    $modx->getOption('kraken.lossy');         // Boolean Yes/No  - default: Yes
$quality	= (int)     $modx->getOption('kraken.lossy_quality'); // Number          - defualt: 75
$resize     = (bool)    $modx->getOption('kraken.resize');        // Boolean Yes/No  - default: No
$width      = (int)     $modx->getOption('kraken.width');         // Number          - defualt: 1600
$height     = (int)     $modx->getOption('kraken.height');        // Number
$strategy   = (string)  $modx->getOption('kraken.strategy');      // String          - default: 'fit'

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
        $sourceImagePath     = MODX_BASE_PATH . $mediaSourceBasePath . $directory . $fileName;
        // Get target image path
        $targetImagePath     = MODX_BASE_PATH . $mediaSourceBasePath . $directory . $fileName;
		
		//Check if file is an actual image 
		$isImage = getimagesize($sourceImagePath) ? true : false;
		if (!$isImage) {
			$modx->log(modX::LOG_LEVEL_ERROR, 'Kraken: '.$file["name"].' is not an image.');
			return;
		}

        //Check if Kraken is enabled through system settings
        if(!$kraken) return;

        $apiEndpoint = "https://api.kraken.io/v1/url"; // A post request will be made to this endpoint

        // Setting compression params
        $params = array(
            "auth" => array(
                "api_key" => $api,
                "api_secret" => $api_secret
            ),
            "url" => $sourceImageUrl,
			"wait" => true
        );

        // Set lossy if enabled
        if ($lossy) {
            $params['lossy'] = true;
            $params['quality'] = $quality;
        };

        // Set resize if enabled
        if ($resize) $params['resize'] = array(
            "width" => $width,
            "height" => $height,
            "strategy" => $strategy
        );
        
        // Uploading the compressed file
        $data = json_encode($params, JSON_FORCE_OBJECT);

        $response = \Httpful\Request::put($apiEndpoint)
            ->sendsJson()                        
            ->body($data)
            ->send(); 

        if ($response->body->success) {
            // optimization succeeded
            file_put_contents($targetImagePath, $response->body->kraked_url);
            $modx->log(modx::LOG_LEVEL_INFO, 'Kraken: Success. Optimized image URL: ' . $response->body->kraked_url);
        } elseif (isset($response->body->message)) {
            // something went wrong with the optimization
            $modx->log(modx::LOG_LEVEL_ERROR, 'Kraken: Fail. Error message: ' . $response->body->message);
        } else {
            // something went wrong with the request
            echo "cURL request failed. Error message: " . $response->body->error;
            $modx->log(modx::LOG_LEVEL_ERROR, "cURL request failed. Error message: " . $response->body->error);
        }
        
        break;
}