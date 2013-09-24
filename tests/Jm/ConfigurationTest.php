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
 * The test uses Mock objects as Jm_Configuration itself is abstract
 *
 * @category  Configuration
 * @package   Jm_Configuration
 * @author    Thorsten Heymann <thorsten@metashock.de>
 * @copyright 2013 Thorsten Heymann <thorsten@metashock.de>
 * @license   BSD-3 http://www.opensource.org/licenses/BSD-3-Clause
 * @version   GIT: $$GITVERSION$$
 * @link      http://www.metashock.de/
 * @since     0.1.1
 */
class Jm_ConfigurationTest extends PHPUnit_Framework_TestCase
{
   
    /**
     */ 
    public function testGetSetHas() {
        $stub = $this->getMockBuilder('Jm_Configuration')
            ->setMethods(NULL)
            ->getMock();

        $this->assertFalse($stub->has('test'));
        $stub->set('test', 1);
        $this->assertTrue($stub->has('test'));
        $this->assertEquals(1, $stub->get('test'));
        $stub->set('foo', 'bar');
        $this->assertEquals('bar', $stub->get('foo'));

        $this->assertEquals(
            array('test' => 1, 'foo' => 'bar'),
            $stub->getAll()
        );
    }


    /**
     * @expectedException Jm_Configuration_KeyNotFoundException
     */
    public function testGetKeyNotFoundException() {
        $stub = $this->getMockBuilder('Jm_Configuration')
            ->setMethods(NULL)
            ->getMock();

        $stub->get('foo');
    }


    /**
     */
    public function testRemove() {
        $stub = $this->getMockBuilder('Jm_Configuration')
            ->setMethods(NULL)
            ->getMock();

        $stub->set('foo', 'bar');
        $this->assertEquals('bar', $stub->get('foo'));
        
        $stub->remove('foo');
        $this->assertEmpty($stub->getAll());
    }


    /**
     * @expectedException Jm_Configuration_KeyNotFoundException
     */
    public function testRemoveKeyNotFoundException() {
        $stub = $this->getMockBuilder('Jm_Configuration')
            ->setMethods(NULL)
            ->getMock();

        $stub->remove('foo');
    }


    /**
     */
    public function testValidateTrue() {
        $conf = $this->getMockBuilder('Jm_Configuration')
            ->setMethods(NULL)
            ->getMock();

        $conf->setValidator('Jm_ConfigurationTest_ConfigTrueValidator');
        $this->assertTrue($conf->isValid());
    }


    /**
     * @expectedException Jm_Configuration_Exception
     */
    public function testValidateException() {
        $conf = $this->getMockBuilder('Jm_Configuration')
            ->setMethods(NULL)
            ->getMock();

        $conf->setValidator('Jm_ConfigurationTest_ConfigFalseValidator');
        $conf->isValid(); // should throw an Exception
    }


    /**
     *
     */
    public function testToString() {
        $conf = $this->getMockBuilder('Jm_Configuration')
            ->setMockClassName('Jm_ConfigurationMock')
            ->setMethods(NULL)
            ->getMock();

        $conf->set('i', 1);
        $conf->set('t', new DateTime('1970/01/01 00:00:00'));
        $conf->set('s', 'bar');
        $conf->set('a', array(1,2,array(1,2)));

        $exptected =
'Jm_ConfigurationMock Object
(
    [__phpunit_invocationMocker:Jm_ConfigurationMock:private] => 
    [values:protected] => Array
        (
            [i] => 1
            [t] => DateTime Object
                (
                    [date] => 1970-01-01 00:00:00
                    [timezone_type] => 3
                    [timezone] => Europe/Berlin
                )

            [s] => bar
            [a] => Array
                (
                    [0] => 1
                    [1] => 2
                    [2] => Array
                        (
                            [0] => 1
                            [1] => 2
                        )

                )

        )

    [validatorClass:protected] => 
    [validator:protected] => 
    [updated:protected] => 1
    [position:protected] => 
)
';
        $str = strval($conf);
        $this->assertEquals($exptected, $str);
    }

    /**
     *
     */
    public function testMerge() {
         $conf = $this->getMockBuilder('Jm_Configuration')
            ->setMethods(NULL)
            ->getMock();

        $conf->set('i', 1);
        $conf->set('t', new DateTime('1970/01/01 00:00:00'));
        $conf->set('s', 'bar');
        $conf->set('a', array(1,2,array(1,2)));

        $confOrig = clone $conf;
        $conf2 = clone $conf;

        $conf->merge($conf2);

        // should be the same as now
        $this->assertEquals($conf->getAll(), $conf2->getAll());

        // change the value of `i`, add and remove a value
        $conf2->set('i', 2);
        $conf2->set('new', 'a');
        $conf2->remove('s');

        $conf->merge($conf2);

        $expected = $conf2->getAll();
        $expected['s'] = 'bar';

        $this->assertEquals($expected, $conf->getAll());

        // try the same with merge left
        $conf = clone $confOrig;

        $expected = $conf->getAll();
        $expected['s'] = 'bar';
        $expected['i'] = 1;
        $expected['new'] = 'a';
 
        $conf->merge($conf2, FALSE);

        $this->assertEquals($expected, $conf->getAll());

        // test if the method accepts an array as param as well
        $toBeMerged = array('hello' => 'world');
        $conf = clone $confOrig;
        $expected = $conf->getAll();
        $expected['hello'] = 'world';
        $conf->merge($toBeMerged);

        $conf->merge($toBeMerged);
        $this->assertEquals($expected, $conf->getAll());
    }
}


/**
 * Helper class for testValidateTrue
 */
class Jm_ConfigurationTest_ConfigTrueValidator
{
    public function validate(Jm_Configuration $config) {
        return TRUE;
    }
}

/**
 * Helper class for testValidateException
 */
class Jm_ConfigurationTest_ConfigFalseValidator
{
    public function validate(Jm_Configuration $config) {
        return FALSE;
    }
}

