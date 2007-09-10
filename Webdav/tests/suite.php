<?php
/**
 * ezcGraphSuite
 *
 * @package Webdav
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Reqiuire base test
 */
require_once 'test_case.php';

/**
 * Require test suites.
 */
require_once 'server_test.php';
require_once 'server_options_test.php';
require_once 'path_factory_test.php';
require_once 'backend_memory_test.php';

require_once 'property_creationdate_test.php';
require_once 'property_displayname_test.php';
require_once 'property_getcontentlanguage_test.php';
require_once 'property_getcontentlength_test.php';
require_once 'property_getcontenttype_test.php';
require_once 'property_getetagtest.php';
require_once 'property_getlastmodified_test.php';
require_once 'property_lockdiscovery_activelock_test.php';
require_once 'property_lockdiscovery_test.php';
require_once 'property_resourcetype_test.php';
require_once 'property_source_link_test.php';
require_once 'property_source_test.php';
require_once 'property_supportedlock_lockentry_test.php';
require_once 'property_test.php';

/**
* Test suite for ImageAnalysis package.
*
 * @package Webdav
 * @subpackage Tests
*/
class ezcWebdavSuite extends PHPUnit_Framework_TestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'Webdav' );

        $this->addTest( ezcWebdavBasicServerTest::suite() );
        $this->addTest( ezcWebdavServerOptionsTest::suite() );
        $this->addTest( ezcWebdavPathFactoryTest::suite() );
        $this->addTest( ezcWebdavMemoryBackendTest::suite() );

        $this->addTest( ezcWebdavCreationdatePropertyTest::suite() );
        $this->addTest( ezcWebdavDisplayNamePropertyTest::suite() );
        $this->addTest( ezcWebdavGetContentLanguagePropertyTest::suite() );
        $this->addTest( ezcWebdavGetContentLengthPropertyTest::suite() );
        $this->addTest( ezcWebdavGetContentTypePropertyTest::suite() );
        $this->addTest( ezcWebdavGetEtagPropertyTest::suite() );
        $this->addTest( ezcWebdavCreationdatePropertyTest::suite() );
        $this->addTest( ezcWebdavCreationlocktypePropertyTest::suite() );
        $this->addTest( ezcWebdavCreationlockentryPropertyTest::suite() );
        $this->addTest( ezcWebdavResourceTypePropertyTest::suite() );
        $this->addTest( ezcWebdavResourceTypePropertyTest::suite() );
        $this->addTest( ezcWebdavResourceTypePropertyTest::suite() );
        $this->addTest( ezcWebdavCreationlocktypePropertyTest::suite() );
    }

    public static function suite()
    {
        return new ezcWebdavSuite( 'ezcWebdavSuite' );
    }
}
?>
