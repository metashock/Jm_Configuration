<?php
/**
 * Jm_Configuration
 *
 * Copyright (c) 2013, Thorsten Heymann <thorsten@metashock.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name Thorsten Heymann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP Version >= 5.3.0
 *
 * @category  Configuration
 * @package   Jm_Configuration
 * @author    Thorsten Heymann <thorsten@metashock.de>
 * @copyright 2013 Thorsten Heymann <thorsten@metashock.de>
 * @license   BSD-3 http://www.opensource.org/licenses/BSD-3-Clause
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/
 * @since     0.1.0
 */
/**
 * Base class for all configuration implementations
 *
 * @category  Configuration
 * @package   Jm_Configuration
 * @author    Thorsten Heymann <thorsten@metashock.de>
 * @copyright 2013 Thorsten Heymann <thorsten@metashock.de>
 * @license   BSD-3 http://www.opensource.org/licenses/BSD-3-Clause
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/
 * @since     0.1.0
 */	
abstract class Jm_Configuration 
{

    /**
     *	One or two dimensional array with 
     *		[section] [key] [value] 
     *	pairs.
     *
     *	@var array(array())
     */
    protected $values;


    /**
     *	
     *
     *	@var string
     */
    protected $validatorClass;


    /**
     *
     *	@var Validator
     */
    protected $validator;



    /**
     * Represents the global configuration
     *
     * @var Jm_Configuration
     */
    protected static $global;


    /**
     * Flag which is set if the original configuration was 
     * updated during runtime.
     *
     * @var boolean
     */
    protected $updated = FALSE;


    /**
     * Sha1sum of the config to identify it faster
     *
     * @var string
     */
    protected $sha1sum;



    /**
     * Constructor
     *
     * @param string $validatorClass Optional name of a validator class
     */
    public function __construct($validatorClass = '') {
        $this->setValidator($validatorClass);
        $this->values = array();
    }



    /**
     *  Acts like print_r()
     *
     *  @return string
     */
    public function __toString() {
        return print_r($this, TRUE);
    }


    /**
     * Returns a config value based on its key
     *  
     * @param string $key The identifier of the config value
     *
     * @return mixed
     */
    public function get($key) {
        if(!isset($this->values[$key])) {
            throw new Exception(sprintf(
                'Config value \'' . $key . '\' was not found'
            ));
        } else {
            return $this->values[$key];
        }
    }


    /**
     * Returns all key value pairs as an one or multi dimensional array.
     * If $this->values hasn't initialized yet it will get 
     * initialized on demand
     *
     * @return array
     */
    public function getAll() {
        if(!is_array($this->values)) {
            $this->values = array();
        }
        return $this->values; 
    }


    /**
     * Validates the configuration using a validator class
     *
     * @return boolean True if the configuration is valid
     *
     * @throws Jm_Configuration_Exception if the configuration is not valid 
     */
    public function validate() {
        // first check if the validaton is 'enabled'
        if(!empty($this->validatorClass)) {
            $this->validator = new $this->validator($this);			
            try{
                $this->validator->validate($this);
            } catch ( Exception $e) {
                // pack the exception info into a 
                // configuration exception and rethrow it
                throw new Jm_Configuration_Exception(
                    'Configuration not valid!'/* @todo extend this */
                );		
            } 
        }	
        return TRUE;
    }


    /**
     * Sets a validator class
     *
     * @param string $className The name of a validation class
     *
     * @return Jm_Configuration
     *
     * @throws InvalidArgumentException if $className is not a string
     */
    public function setValidator($className) {
        Jm_Util_Checktype::check('string', $className);
        $this->validatorClass = $className;
        return $this;
    }


    /**	
     *	Returns the sha1sum for the configuration.
     *	Can be useful in factory/singleton methods
     *	where eg. only one object per configration should
     *	be created ...
     *
     *	@return string
     */
    public function sha1sum() {
        if($this->updated || empty($this->sha1sum)) {
            $str = '';
            foreach($this->values as $key => $value) {
                $str .= $key.$value;
            }
            $this->sha1sum = sha1($str);
        }
        return $this->sha1sum;
    }


    /**
     *  Merges the other configuration from
     *  right(default) or left 
     *
     *  @param Jm_Configuration|array $configuration Another Jm_Configuration
     *  object or an array
     *  @param boolean                $mergeright    If passing FALSE the 
     *  current values will not being overwritten. Defaults to TRUE
     *
     *  @return Jm_Configuration
     *
     *  @throws InvalidArgumentException 
     */
    public function merge (
        $configuration,
        $mergeright = TRUE
    ) {
        if(is_array($configuration)) {
            $configuration = new Jm_Configuration_Array($configuration);
        }

        if(!($configration instanceof Jm_Configuration)) {
            throw new InvalidArgumentException ( sprintf (
                '$configuration expected to be Jm_Configuration or array. '
              . '%s found', is_object($configration) 
                ? get_class($configuration) : gettype($configuration)
            ));
        }

        if($mergeright === TRUE) {
            $this->values = array_merge (
                $this->values, $configuration->values()
            );
        } else {
            $this->values = array_merge (
                $configuration->values(), $this->values()
            );  
        }
        return $this;
    }
}

