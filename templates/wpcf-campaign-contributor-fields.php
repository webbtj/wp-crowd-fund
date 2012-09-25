<?php
/*
This is not really a template file, but it is still overrideable by making a copy into your theme's directory.
This allows you to create additional fields that will be collected from contributors, these fields are global (apply to all perks in all campaigns)
*/
$wpcf_contributor_fields = array(
	array('id' => 'address1',		'label' => __('Address 1', 'wp crowd fund')),
	array('id' => 'address2',		'label' => __('Address 2', 'wp crowd fund')),
	array('id' => 'city',			'label' => __('City', 'wp crowd fund')),
	array('id' => 'province',		'label' => __('State/Province', 'wp crowd fund')),
	array('id' => 'country',		'label' => __('Country', 'wp crowd fund')),
	array('id' => 'postal_code',	'label' => __('Zip/Postal Code', 'wp crowd fund')),
);