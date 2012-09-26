<?php
/*
This is not really a template file, but it is still overrideable by making a copy into your theme's directory.
This allows you to create additional fields that will be collected from contributors, these fields are global (apply to all perks in all campaigns)
*/
$wpcf_contributor_fields = array(
	'address1'		=> array('required' => true, 'required_anonymous' => false, 'label' => __('Address 1', 'wp crowd fund')),
	'address2'		=> array('required' => true, 'required_anonymous' => false, 'label' => __('Address 2', 'wp crowd fund')),
	'city'			=> array('required' => true, 'required_anonymous' => false, 'label' => __('City', 'wp crowd fund')),
	'province'		=> array('required' => true, 'required_anonymous' => false, 'label' => __('State/Province', 'wp crowd fund')),
	'country'		=> array('required' => true, 'required_anonymous' => true , 'label' => __('Country', 'wp crowd fund')),
	'postal_code'	=> array('required' => true, 'required_anonymous' => false, 'label' => __('Zip/Postal Code', 'wp crowd fund')),
);