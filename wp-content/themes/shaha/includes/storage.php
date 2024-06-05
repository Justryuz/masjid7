<?php
/**
 * Theme storage manipulations
 *
 * @package WordPress
 * @subpackage Shaha
 * @since Shaha 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('shaha_storage_get')) {
	function shaha_storage_get($var_name, $default='') {
		global $SHAHA_STORAGE;
		return isset($SHAHA_STORAGE[$var_name]) ? $SHAHA_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('shaha_storage_set')) {
	function shaha_storage_set($var_name, $value) {
		global $SHAHA_STORAGE;
		$SHAHA_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('shaha_storage_empty')) {
	function shaha_storage_empty($var_name, $key='', $key2='') {
		global $SHAHA_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($SHAHA_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($SHAHA_STORAGE[$var_name][$key]);
		else
			return empty($SHAHA_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('shaha_storage_isset')) {
	function shaha_storage_isset($var_name, $key='', $key2='') {
		global $SHAHA_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($SHAHA_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($SHAHA_STORAGE[$var_name][$key]);
		else
			return isset($SHAHA_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('shaha_storage_inc')) {
	function shaha_storage_inc($var_name, $value=1) {
		global $SHAHA_STORAGE;
		if (empty($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = 0;
		$SHAHA_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('shaha_storage_concat')) {
	function shaha_storage_concat($var_name, $value) {
		global $SHAHA_STORAGE;
		if (empty($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = '';
		$SHAHA_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('shaha_storage_get_array')) {
	function shaha_storage_get_array($var_name, $key, $key2='', $default='') {
		global $SHAHA_STORAGE;
		if ( '' === $key2 ) {
			return ! empty( $var_name ) && '' !== $key && isset( $SHAHA_STORAGE[ $var_name ][ $key ] ) ? $SHAHA_STORAGE[ $var_name ][ $key ] : $default;
		} else {
			return ! empty( $var_name ) && '' !== $key && isset( $SHAHA_STORAGE[ $var_name ][ $key ][ $key2 ] ) ? $SHAHA_STORAGE[ $var_name ][ $key ][ $key2 ] : $default;
		}
	}
}

// Set array element
if (!function_exists('shaha_storage_set_array')) {
	function shaha_storage_set_array($var_name, $key, $value) {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if ($key==='')
			$SHAHA_STORAGE[$var_name][] = $value;
		else
			$SHAHA_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('shaha_storage_set_array2')) {
	function shaha_storage_set_array2($var_name, $key, $key2, $value) {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if (!isset($SHAHA_STORAGE[$var_name][$key])) $SHAHA_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$SHAHA_STORAGE[$var_name][$key][] = $value;
		else
			$SHAHA_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Merge array elements
if (!function_exists('shaha_storage_merge_array')) {
	function shaha_storage_merge_array($var_name, $key, $value) {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if ($key==='')
			$SHAHA_STORAGE[$var_name] = array_merge($SHAHA_STORAGE[$var_name], $value);
		else
			$SHAHA_STORAGE[$var_name][$key] = array_merge($SHAHA_STORAGE[$var_name][$key], $value);
	}
}

// Add array element after the key
if (!function_exists('shaha_storage_set_array_after')) {
	function shaha_storage_set_array_after($var_name, $after, $key, $value='') {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if (is_array($key))
			shaha_array_insert_after($SHAHA_STORAGE[$var_name], $after, $key);
		else
			shaha_array_insert_after($SHAHA_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('shaha_storage_set_array_before')) {
	function shaha_storage_set_array_before($var_name, $before, $key, $value='') {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if (is_array($key))
			shaha_array_insert_before($SHAHA_STORAGE[$var_name], $before, $key);
		else
			shaha_array_insert_before($SHAHA_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('shaha_storage_push_array')) {
	function shaha_storage_push_array($var_name, $key, $value) {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($SHAHA_STORAGE[$var_name], $value);
		else {
			if (!isset($SHAHA_STORAGE[$var_name][$key])) $SHAHA_STORAGE[$var_name][$key] = array();
			array_push($SHAHA_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('shaha_storage_pop_array')) {
	function shaha_storage_pop_array($var_name, $key='', $defa='') {
		global $SHAHA_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($SHAHA_STORAGE[$var_name]) && is_array($SHAHA_STORAGE[$var_name]) && count($SHAHA_STORAGE[$var_name]) > 0)
				$rez = array_pop($SHAHA_STORAGE[$var_name]);
		} else {
			if (isset($SHAHA_STORAGE[$var_name][$key]) && is_array($SHAHA_STORAGE[$var_name][$key]) && count($SHAHA_STORAGE[$var_name][$key]) > 0)
				$rez = array_pop($SHAHA_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('shaha_storage_inc_array')) {
	function shaha_storage_inc_array($var_name, $key, $value=1) {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if (empty($SHAHA_STORAGE[$var_name][$key])) $SHAHA_STORAGE[$var_name][$key] = 0;
		$SHAHA_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('shaha_storage_concat_array')) {
	function shaha_storage_concat_array($var_name, $key, $value) {
		global $SHAHA_STORAGE;
		if (!isset($SHAHA_STORAGE[$var_name])) $SHAHA_STORAGE[$var_name] = array();
		if (empty($SHAHA_STORAGE[$var_name][$key])) $SHAHA_STORAGE[$var_name][$key] = '';
		$SHAHA_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('shaha_storage_call_obj_method')) {
	function shaha_storage_call_obj_method($var_name, $method, $param=null) {
		global $SHAHA_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($SHAHA_STORAGE[$var_name]) ? $SHAHA_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($SHAHA_STORAGE[$var_name]) ? $SHAHA_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('shaha_storage_get_obj_property')) {
	function shaha_storage_get_obj_property($var_name, $prop, $default='') {
		global $SHAHA_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($SHAHA_STORAGE[$var_name]->$prop) ? $SHAHA_STORAGE[$var_name]->$prop : $default;
	}
}
?>