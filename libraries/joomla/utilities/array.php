<?php
/**
 * @version $Id: functions.php 4277 2006-07-19 20:35:35Z friesengeist $
 * @package		Joomla.Framework
 * @subpackage	Utilities
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

/**
 * JArrayHelper is an array utility class for doing all sorts of odds and ends with arrays.
 * 
 * @static
 * @author		Louis Landry <louis.landry@joomla.org>
 * @package 	Joomla.Framework
 * @subpackage 	Utilities
 * @since		1.5
 */
class JArrayHelper
{
	/**
	 * Function to convert array to integer values
	 *
	 * @static
	 * @param	array	$array		The source array to convert
	 * @param	int		$default	A default value to assign if $array is not an array
	 * @return	array
	 * @since	1.5
	 */
	function toInts(& $array, $default = null)
	{
		if (is_array($array)) {
			$n = count($array);
			for ($i = 0; $i < $n; $i++)
			{
				$array[$i] = intval($array[$i]);
			}
		} else {
			if (is_null($default)) {
				return array();
			} else {
				return array($default);
			}
		}
	}

	/**
	 * Utility function to map an array to a stdClass object.
	 *
	 * @static
	 * @param	array	$array	The array to map.
	 * @return	object	The object mapped from the given array.
	 * @since	1.5
	 */
	function toObject($array)
	{
		$obj = null;
		if (is_array($array)) {
			$obj = new stdClass();
			foreach ($array as $k => $v)
			{
				if (is_array($v)) {
					$obj-> $k = JArrayHelper::toObject($v);
				} else {
					$obj-> $k = $v;
				}
			}
		}
		return $obj;
	}

	/**
	 * Extracts a column from an array of arrays or objects
	 * 
	 * @static
	 * @param	array	$array	The source array
	 * @param	string	$index	The index of the column or name of object property
	 * @return	array	Column of values from the source array
	 * @since	1.5
	 */
	function getColumn(&$array, $index)
	{
		$result = array ();

		if (is_array($array))
		{
			$n = count($array);
			for ($i = 0; $i < $n; $i++)
			{
				$item = & $array[$i];
				if (is_array($item) && isset ($item[$index])) {
					$result[] = $item[$index];
				} elseif (is_object($item) && isset ($item-> $index)) {
					$result[] = $item-> $index;
				}
				// else ignore the entry
			}
		}
		return $result;
	}

	/**
	 * Utility function to return a value from a named array or a specified default
	 * 
	 * @static
	 * @param	array	$arr		A named array
	 * @param	string	$name		The key to search for
	 * @param	mixed	$default	The default value to give if no key found
	 * @param	string	$type		Return type for the variable (INT, FLOAT, STRING, BOOLEAN, ARRAY)
	 * @return	mixed	The value from the source array
	 * @since	1.5
	 */
	function getValue(&$arr, $name, $default=null, $type='')
	{
		// Initialize variables
		$type = strtoupper($type);
		$result = null;

		if (isset ($arr[$name])) {
			$result = $arr[$name];
		}

		// Handle the default case
		if ((empty ($result))) {
			$result = $default;
		}

		// Handle the type constraint
		switch ($type)
		{
			case 'INT' :
			case 'INTEGER' :
				// Only use the first integer value
				@ preg_match('/-?[0-9]+/', $result, $matches);
				$result = @ (int) $matches[0];
				break;

			case 'FLOAT' :
			case 'DOUBLE' :
				// Only use the first floating point value
				@ preg_match('/-?[0-9]+(\.[0-9]+)?/', $result, $matches);
				$result = @ (float) $matches[0];
				break;

			case 'BOOL' :
			case 'BOOLEAN' :
				$result = (bool) $result;
				break;

			case 'ARRAY' :
				if (!is_array($result)) {
					$result = array ($result);
				}
				break;

			case 'STRING' :
				$result = (string) $result;
				break;

			case 'NONE' :
			default :
				// No casting necessary
				break;
		}
		return $result;
	}

	/**
	 * Utility function to sort an array of objects on a given field
	 * 
	 * @static
	 * @param	array	$arr		An array of objects
	 * @param	string	$k			The key to sort on
	 * @param	int		$direction	Direction to sort in [1 = Ascending] [-1 = Descending]
	 * @return	array	The sorted array of objects
	 * @since	1.5
	 */
	function sortObjects( &$a, $k, $direction=1 )
	{
		$GLOBALS['JAH_so'] = array(
			'key'		=> $k,
			'direction'	=> $direction
		);
		usort( $a, array('JArrayHelper', '_sortObjects') );
		unset( $GLOBALS['JAH_so'] );

		return $a;
	}

	/**
	 * Private callback function for sorting an array of objects on a key
	 * 
	 * @static
	 * @param	array	$a	An array of objects
	 * @param	array	$b	An array of objects
	 * @return	int		Comparison status
	 * @since	1.5
	 * @see		JArrayHelper::sortObjects()
	 */
	function _sortObjects( &$a, &$b )
	{
		$params = $GLOBALS['JAH_so'];	
		if ( $a->$params['key'] > $b->$params['key'] ) {
			return $params['direction'];
		}
		if ( $a->$params['key'] < $b->$params['key'] ) {
			return -1 * $params['direction'];
		}
		return 0;
	}
}
?>