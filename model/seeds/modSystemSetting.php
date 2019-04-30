<?php
/*-----------------------------------------------------------------
 * Lexicon keys for System Settings follows this format:
 * Name: setting_ + $key
 * Description: setting_ + $key + _desc
 -----------------------------------------------------------------*/
return array(

    array(
        'key'  		=>     'kraken.api_key',
		'value'		=>     '',
		'xtype'		=>     'textfield',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:default'
    ),
    array(
        'key'  		=>     'kraken.api_secret',
		'value'		=>     '',
		'xtype'		=>     'textfield',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:default'
    ),
);
/*EOF*/