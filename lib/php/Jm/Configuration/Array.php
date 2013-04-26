<?php
/**
 *
 *  @package Jm_Configuration
 */
/**
 *
 *  @package Jm_Configuration
 */
class Jm_Configuration_Array extends Jm_Configuration
{

	/**
	 *  @param array $values
	 */
	public function __construct(array $values = array()) {
		$this->values = $values;
	}

}


/**
 * Syntax sugar for constructor
 *
 * @param array $values Optional array with initial values
 *
 * @returns Jm_Configuration_Array
 */
function jm_conf_arr(array $values = array()) {
    return new Jm_Configuration_Array($values);
}

