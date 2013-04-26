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
 * PHPUnit test for Jm_Configuration_Array
 */
class Jm_Configuration_ArrayTest extends PHPUnit_Framework_TestCase
{


    public function setUp() {
        require_once 'Jm/Autoloader.php';
    }


    /**
     *
     */
    public function testConstruct() {

        // we test with an empty array first
        $conf = new Jm_Configuration_Array();
        $this->assertEmpty($conf->getAll());

        // now we test we a predefined array
        $conf = new Jm_Configuration_Array(array(
            'a' => 1,
            'b' => 'test'
        ));

        $this->assertEquals($conf->get('a'), 1);
        $this->assertEquals($conf->get('b'), 'test');

        $this->assertEquals($conf->getAll(), array('a' => 1, 'b' => 'test'));

    }


    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testConstructorException() {
        $conf = new Jm_Configuration_Array('');
    }


    /**
     *
     */
    public function test_jm_conf_arr() {

        // if it runs with process isolation the autoloader
        // won't been called as we only call the function
        require_once 'Jm/Configuration/Array.php';

        // we test with an empty array first
        $conf = jm_conf_arr();
        $this->assertEmpty($conf->getAll());

        // now we test we a predefined array
        $conf = jm_conf_arr(array(
            'a' => 1,
            'b' => 'test'
        ));

        $this->assertEquals($conf->get('a'), 1);
        $this->assertEquals($conf->get('b'), 'test');

        $this->assertEquals($conf->getAll(), array('a' => 1, 'b' => 'test'));
    }


    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function test_jm_conf_arrException() {

        // if it runs with process isolation the autoloader
        // won't been called as we only call the function
        require_once 'Jm/Configuration/Array.php';

        $conf = jm_conf_arr('');
    }


}
