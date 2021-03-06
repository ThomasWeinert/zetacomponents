<?php
/**
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @filesource
 * @package Authentication
 * @version //autogen//
 * @subpackage Tests
 */

include_once( 'Authentication/tests/test.php' );
include_once( 'data/encryption.php' );

/**
 * @package Authentication
 * @version //autogen//
 * @subpackage Tests
 */
class ezcAuthenticationTokenTest extends ezcAuthenticationTest
{
    public static function suite()
    {
        return new PHPUnit_Framework_TestSuite( "ezcAuthenticationTokenTest" );
    }

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    public function testTokenNull()
    {
        $credentials = new ezcAuthenticationIdCredentials( null );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->addFilter( new ezcAuthenticationTokenFilter( 'b1b3773a05c0ed0176787a4f1574ff0075f7521e', 'sha1' ) );
        $this->assertEquals( false, $authentication->run() );
    }

    public function testTokenSha1Correct()
    {
        $credentials = new ezcAuthenticationIdCredentials( 'qwerty' );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->addFilter( new ezcAuthenticationTokenFilter( 'b1b3773a05c0ed0176787a4f1574ff0075f7521e', 'sha1' ) );
        $this->assertEquals( true, $authentication->run() );
    }

    public function testTokenSha1Fail()
    {
        $credentials = new ezcAuthenticationIdCredentials( 'qwerty' );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->addFilter( new ezcAuthenticationTokenFilter( 'wrong value', 'sha1' ) );
        $this->assertEquals( false, $authentication->run() );
    }

    public function testTokenMd5Correct()
    {
        $credentials = new ezcAuthenticationIdCredentials( 'asdfgh' );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->addFilter( new ezcAuthenticationTokenFilter( 'a152e841783914146e4bcd4f39100686', 'md5' ) );
        $this->assertEquals( true, $authentication->run() );
    }

    public function testTokenMd5Fail()
    {
        $credentials = new ezcAuthenticationIdCredentials( 'qwerty' );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->addFilter( new ezcAuthenticationTokenFilter( 'wrong value', 'md5' ) );
        $this->assertEquals( false, $authentication->run() );
    }

    public function testTokenExternCallbackCorrect()
    {
        $credentials = new ezcAuthenticationIdCredentials( 'foobar' );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->addFilter( new ezcAuthenticationTokenFilter( 'xxIh4TUllUASg', array( 'EncryptionTest', 'uncrackable' ) ) );
        $this->assertEquals( true, $authentication->run() );
    }

    public function testTokenExternCallbackFail()
    {
        $credentials = new ezcAuthenticationIdCredentials( 'foobar' );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->addFilter( new ezcAuthenticationTokenFilter( 'wrong value', array( 'EncryptionTest', 'uncrackable' ) ) );
        $this->assertEquals( false, $authentication->run() );
    }

    public function testTokenOptions()
    {
        $options = new ezcAuthenticationTokenOptions();

        $this->missingPropertyTest( $options, 'no_such_option' );
    }

    public function testTokenOptionsGetSet()
    {
        $options = new ezcAuthenticationTokenOptions();

        $filter = new ezcAuthenticationTokenFilter( '', 'md5' );
        $filter->setOptions( $options );
        $this->assertEquals( $options, $filter->getOptions() );
    }

    public function testTokenProperties()
    {
        $token = '';
        $filter = new ezcAuthenticationTokenFilter( $token, 'md5' );

        $this->invalidPropertyTest( $filter, 'token', array(), 'string || int' );
        $this->invalidPropertyTest( $filter, 'function', 'no_such_function', 'callback' );
        $this->invalidPropertyTest( $filter, 'function', array( 'EncryptionTest', 'no_such_function' ), 'callback' );
        $this->missingPropertyTest( $filter, 'no_such_property' );
    }

    public function testTokenPropertiesIsSet()
    {
        $token = '';
        $filter = new ezcAuthenticationTokenFilter( $token, 'md5' );

        $this->issetPropertyTest( $filter, 'token', true );
        $this->issetPropertyTest( $filter, 'function', true );
        $this->issetPropertyTest( $filter, 'no_such_property', false );
    }
}
?>
