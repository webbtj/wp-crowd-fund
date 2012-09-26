<?php
/*
This is not really a template file, but it is still overrideable by making a copy into your theme's directory.
This allows you to create additional fields that will be collected from contributors, these fields are global (apply to all perks in all campaigns)
*/
$wpcf_contributor_fields = array(
	'address1'		=> __('Address 1', 'wp crowd fund'),
	'address2'		=> __('Address 2', 'wp crowd fund'),
	'city'			=> __('City', 'wp crowd fund'),
	'province'		=> __('State/Province', 'wp crowd fund'),
	'country'		=> __('Country', 'wp crowd fund'),
	'postal_code'	=> __('Zip/Postal Code', 'wp crowd fund'),
);