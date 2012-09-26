<?php

function wpcf_perk_error($error, $echo=true){
	if(!isset($error['message']))
		return false;
	if($echo)
		echo $error['message'];
	else
		return $error['message'];
}

function __wpcf_contribute_text_input($id, $echo=true, $id_only=false, $class=false){
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<input type="text" name="' . $id . '" id="' . $id . '" ' . $class . ' />';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

function __wpcf_contribute_textarea_input($id, $echo=true, $id_only=false, $class=false){
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<textarea type="text" name="' . $id . '" id="' . $id . '" ' . $class . '></textarea>';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

function __wpcf_contribute_checkbox_input($id, $echo=true, $id_only=false, $class=false){
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
	}else{
		$class = $class ? ' class="' . $class . '" ' : '';
		$output = '<input type="checkbox" value="1" name="' . $id . '" id="' . $id . '" ' . $class . '>';
		if($echo)
			echo $output;
		else
			return $output;
	}
}

function _wpcf_contributor_field($type, $name, $echo=true, $id_only=false){
	global $wpcf_contributor_fields;
	if(!isset($wpcf_contributor_fields)){
		include(wpcf_template_include(dirname(dirname(__FILE__)).'/templates/wpcf-campaign-contributor-fields.php'));
	}
	$id = array_key_exists($name, $wpcf_contributor_fields) ? 'wpcf-contribute-' . $name : '';

	$class = '';
	$class .= $wpcf_contributor_fields[$name]['required'] ? ' wpcf-required ' : ' wpcf-not-required ';
	$class .=  $wpcf_contributor_fields[$name]['required_anonymous'] ? ' wpcf-required-anonymous ' : ' wpcf-not-required-anonymous ';
	$class = $class ? ' class="' . $class . '" ' : '';
	if($id_only){
		if($echo)
			echo $id;
		else
			return $id;
		return;
	}elseif($id){
		$html = '';
		switch($type){
			case 'text':
				$html = '<input ' . $class . ' type="text" name="' . $id . '" id="' . $id . '" />';
				break;
			case 'textarea':
				$html = '<textarea ' . $class . ' name="' . $id . '" id="' . $id . '"></textarea>';
				break;
			case 'label':
				$html = '<label for="'.$id.'">' . $wpcf_contributor_fields[$name]['label'] . '</label>';
				break;
		}
		if($echo)
			echo $html;
		else
			return $html;
	}
}