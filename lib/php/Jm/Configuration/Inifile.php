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
 * Implements .ini file configuration parsing
 *
 * @package Jm_Configuration
 */
class Jm_Configuration_Inifile extends Jm_Configuration
{
        
    /**
     *
     *  @var array(string)
     */
     protected $paths;



    /**
     * Boolean flag which indicates whether sections should 
     * be processed or not
     * 
     * @var boolean
     */
    protected $processSections;



    /**
     * Constructor
     *
     * @param string  $path            A path to an ini file or a directory 
     *                                 which can contain multiple ini files
     *                                 and subdirectories with ini files
     * @param boolean $processSections Should sections `[SectionName]` being 
     *                                 processed? Default is FALSE
     * @param string  $validatorClass  A validator class name. Optional
     *
     * @return Jm_Configuration_Inifile
     *
     * @throws Exception                If $path does not exists or is not 
     *                                  readable or executable if it is a 
     *                                  directory
     * @throws InvalidArgumentException If any of the params' types mismatches
     */ 
    public function __construct(
        $path = '', 
        $processSections = FALSE,
        $validatorClass = ''
    ) {
        Jm_Util_Checktype::check('string', $path);
        parent::__construct($validatorClass);
        // set instance vars
        $this->setProcessSections($processSections);
        // load and validate
        if(!empty($path)) {
            $this->load($path); 
        }
    }


    /**
     * Loads configuration values from an inifile. The file format
     * is exopected to be the sames as the php.ini.
     *
     * @param string  $path  A file name
     * @param boolean $merge Should should exsiting values being replaced
     *                        getting merged with the values from $path?
     *
     * @return Jm_Configuration_Inifile
     *
     * @throws Exception if $path isn't readable
     */
    protected function loadFile($path, $merge = TRUE) {
        if(!is_readable($path)) {
            throw new Exception('Cannot read from \'' . $path . '\'');
        }
        $this->values = array_merge_recursive(
            $this->values,
            parse_ini_file($path)
        );
        // validate
        $this->validate();

        return $this;
    }


    /**
     * Loads configuration values from inifiles. The file format
     * is exopected the sames as the php.ini. $path can be either 
     * a filename or a directory. If its a directory, then the directory
     * will be recursively traversed and all *.ini files parsed and 
     * merged into a configuration array.
     *
     * @param string $path Either a file name or a directory 
     *
     * @return Jm_Configuration_Inifile
     *
     * @throws Exception if $path is a filename and the file isn't readable
     */
    public function load($path) {
        if(!file_exists($path)) {
            throw new Exception('$path: ' . $path . ' does not exists');
        }
       
        if(is_dir($path)) {
            $this->loadDirectory($path);
        } else {
            $this->loadFile($path);
        }

        return $this;
    }


    /**
     * Loads configuration files from a whole directory. Files will be parsed 
     * in that order in which the filesystem returns them.
     *
     * @param string  $path      Path to a directory
     * @param string  $pattern   Regex pattern for files that should be parsed
     *                           defualts to *.ini files
     * @param boolean $recursive Should the directory be scanned recursively
     *                           for configuration files or just first level?
     *
     * @return Jm_Configuration_Ininfile
     */
    protected function loadDirectory(
        $path,
        $pattern = '/.*\.ini$/',
        $recursive = TRUE
    ) {
        $this->values = array();
        $paths = array();
        $directoryIterator = new RecursiveDirectoryIterator($path);
        $recursiveIterator = new RecursiveIteratorIterator($directoryIterator);

        $pattern = '/^.+\.ini$/i';
        $regexIterator = new RegexIterator($recursiveIterator, $pattern);


        foreach($dir->ls('/^.+\.ini$/i', TRUE) as $path) {
            if(is_file($path) && is_readable($path)) {      
                $vals = parse_ini_file (
                    $path, 
                    $this->processSections
                );
                // store the value in filenames section 
                // so it will be always accessible
                $paths[$path] = $vals;
                    
                // merge the values into the values array
                if(is_array($vals)){
                    $this->values = array_merge_recursive(
                        $this->values, 
                        $vals
                    );
                }               
            }
        }       
    }


    /**
     * Setter for the process sections property.
     *
     * @param boolean $value If true sections like `[SectionName]` 
     *                       .ini files will be processed.
     *
     * @return Jm_Configuration_Inifile
     */
    protected function setProcessSections($value){
        Jm_Util_Checktype::check('boolean', $value);
        $this->processSections = $value;
    }
}

