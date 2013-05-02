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
 * Test custom methods of Jm_Configuration_Inifile
 */
class Jm_Configuration_InifileTest extends PHPUnit_Framework_TestCase
{


    /**
     *
     */
    public function testConstructor() {
        $inifile = tempnam(sys_get_temp_dir(), 'phpunit');
        register_shutdown_function(function() use($inifile) {
            unlink($inifile);
        });


        file_put_contents($inifile, <<<EOF
a=1
b="test"
EOF
);
        $conf = new Jm_Configuration_Inifile($inifile);  

        $this->assertEquals($conf->get('a'), 1);
        $this->assertEquals($conf->get('b'), 'test');

        $this->assertEquals($conf->getAll(), array('a' => 1, 'b' => 'test'));
    }


    /**
     * Tests that load() will throw an Exception if $path does not exist
     *
     * @expectedException Jm_Filesystem_FileNotFoundException
     */
    public function testLoadFileNotFoundException() {
        $conf = new Jm_Configuration_Inifile();
        $conf->load(sha1(uniqid())); // does not exist
    }


    /**
     * Tests that load() will throw an Exception if $path is not readable
     *
     * @expectedException Jm_Filesystem_FileNotReadableException
     */
    public function testLoadFileNotReadableException() {
        $inifile = tempnam(sys_get_temp_dir(), 'phpunit');
        register_shutdown_function(function() use($inifile) {
            unlink($inifile);
        });

        chmod($inifile, 0000);
        $conf = new Jm_Configuration_Inifile($inifile);
    }


    /**
     * @expectedException Jm_Configuration_InifileCorruptException
     */
    public function testLoadInifileCorruptException() {
        $inifile = tempnam(sys_get_temp_dir(), 'phpunit');
        register_shutdown_function(function() use($inifile) {
            unlink($inifile);
        });
        file_put_contents($inifile, '=test');
        $conf = new Jm_Configuration_Inifile($inifile);
    }


    /**
     */
    public function testLoadDirectory() {
        // potential race condition!
        $aini = tempnam(sys_get_temp_dir(), 'a.phpunit');
        $bini = tempnam(sys_get_temp_dir(), 'b.phpunit');
        rename($aini, $aini . '.ini');
        rename($bini, $bini . '.ini');
        $aini = $aini . '.ini';
        $bini = $bini . '.ini';
        
        register_shutdown_function(function() use($aini, $bini) {
            unlink($aini);
            unlink($bini);
        });

        file_put_contents($aini, <<<EOF
foo=bar
hello=world
EOF
        );

        file_put_contents($bini, <<<EOF
foo=
test=1
EOF
        );

        $conf = new Jm_Configuration_Inifile(sys_get_temp_dir());

        $expected = array (
            'foo' => '',
            'hello' => 'world',
            'test' => '1' 
        );

        $this->assertEquals($expected, $conf->getAll());

    }
}

