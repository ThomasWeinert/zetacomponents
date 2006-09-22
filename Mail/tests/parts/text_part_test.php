<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Mail
 * @subpackage Tests
 */


/**
 * @package Mail
 * @subpackage Tests
 */
class ezcMailTextTest extends ezcTestCase
{
    private $part;

	protected function setUp()
	{
        $this->part = new ezcMailText( "dummy" );
	}

    /**
     * Test that the constuctor eats parameters like it should
     */
    public function testConstructor()
    {
        $this->part = new ezcMailText( "TestText", "ISO-String", ezcMail::BASE64 );
        $this->assertEquals( "TestText", $this->part->text );
        $this->assertEquals( "ISO-String", $this->part->charset );
        $this->assertEquals( ezcMail::BASE64, $this->part->encoding );
    }

    /**
     * Tests if headers are generated as expected by the TextPart.
     * It should include both extra headers set manually and content type
     * and encoding headers
     */
    public function testGenerateHeaders()
    {
        $expectedResult = "X-Extra: Test" . ezcMailTools::lineBreak() .
                          "Content-Type: text/plain; charset=us-ascii" . ezcMailTools::lineBreak() .
                          "Content-Transfer-Encoding: 8bit" . ezcMailTools::lineBreak();

        $this->part->setHeader( "X-Extra", "Test" );
        $this->assertEquals( $expectedResult, $this->part->generateHeaders() );
    }

    /**
     * Tests for properties
     */
    public function testGetProperties()
    {
        $temp = new ezcMailText( 'dummy', 'utf-8', ezcMail::EIGHT_BIT, 'iso-8859-2' );
        $this->assertEquals( 'utf-8', $temp->charset );
        $this->assertEquals( 'iso-8859-2', $temp->originalCharset );
        $this->assertEquals( ezcMail::EIGHT_BIT, $temp->encoding );
        $this->assertEquals( 'plain', $temp->subType );
        $this->assertEquals( 'dummy', $temp->text );
        $this->assertEquals(
            new ezcMailHeadersHolder(),
            $temp->headers
        );
    }

    public function testSetProperties()
    {
        $temp = new ezcMailText( 'dummy', 'bogus', -1, 'iso-8859-2' );
        $temp->charset = 'utf-8';
        $temp->encoding = ezcMail::EIGHT_BIT;
        $temp->subType = 'html';
        $temp->text = 'new dummy';
        try
        {
            $temp->originalCharset = 'iso-8859-5';
            $this->fail( 'Expected exception not thrown' );
        }
        catch ( ezcBasePropertyPermissionException $e )
        {
            $this->assertEquals( 'The property <originalCharset> is read-only.', $e->getMessage() );
        }

        $this->assertEquals( 'utf-8', $temp->charset );
        $this->assertEquals( 'iso-8859-2', $temp->originalCharset );
        $this->assertEquals( ezcMail::EIGHT_BIT, $temp->encoding );
        $this->assertEquals( 'html', $temp->subType );
        $this->assertEquals( 'new dummy', $temp->text );
    }

    public static function suite()
    {
         return new ezcTestSuite( "ezcMailTextTest" );
    }
}
?>
