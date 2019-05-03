<?php
/*-----------------------------------------------------------------
 * Lexicon keys for System Settings follows this format:
 * Name: setting_ + $key
 * Description: setting_ + $key + _desc
 -----------------------------------------------------------------*/
return array(
	array(
        'key'  		=>     'kraken.enable',
		'value'		=>     '0',
		'xtype'		=>     'combo-boolean',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:default'
    ),
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
	array(
        'key'  		=>     'kraken.lossy',
		'value'		=>     '1',
		'xtype'		=>     'combo-boolean',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:quality'
	),
    array(
        'key'  		=>     'kraken.lossy_quality',
		'value'		=>     '75',
		'xtype'		=>     'numberfield',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:quality'
    ),
	array(
        'key'  		=>     'kraken.resize',
		'value'		=>     '0',
		'xtype'		=>     'combo-boolean',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:resize'
	),
	array(
        'key'  		=>     'kraken.width',
		'value'		=>     '1600',
		'xtype'		=>     'numberfield',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:resize'
	),
	array(
        'key'  		=>     'kraken.height',
		'value'		=>     '',
		'xtype'		=>     'numberfield',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:resize'
	),
	array(
        'key'  		=>     'kraken.strategy',
		'value'		=>     'auto',
		'xtype'		=>     'textfield',
		'namespace' => 'kraken',
		'area' 		=> 'kraken:resize'
    ),
);
/*EOF*/