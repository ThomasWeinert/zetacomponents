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
 * @version //autogentag//
 * @filesource
 * @package PersistentObject
 * @subpackage Tests
 */

require_once "data/persistent_test_object.php";

/**
 * Tests the find iterator.
 *
 * @package PersistentObject
 * @subpackage Tests
 */
class ezcPersistentFindIteratorTest extends ezcTestCase
{
    private $db;
    private $manager;
    private $session;

    protected function setUp()
    {
        try
        {
            $this->db = ezcDbInstance::get();
        }
        catch ( Exception $e )
        {
            $this->markTestSkipped( 'There was no database configured' );
        }

        PersistentTestObject::setupTable();
        PersistentTestObject::insertCleanData();
        $this->manager = new ezcPersistentCodeManager( dirname( __FILE__ ) . "/data/" );
        $this->session = new ezcPersistentSession( $this->db, $this->manager );
    }

    protected function tearDown()
    {
        PersistentTestObject::cleanup();
    }

    public static function suite()
    {
        return new PHPUnit_Framework_TestSuite( 'ezcPersistentFindIteratorTest' );
    }

    public function testFaultyStmtReturnsNull()
    {
        $it = new ezcPersistentFindIterator( $this->db->prepare( "select * from " . $this->db->quoteIdentifier( "PO_test" ) ),
                                             $this->manager->fetchDefinition( 'PersistentTestObject' ) );
        $this->assertEquals( null, $it->current() );
        $this->assertEquals( null, $it->next() );
        $this->assertEquals( false, $it->valid() );
    }

    public function testFaultyDefinitionReturnsNull()
    {
        $it = new ezcPersistentFindIterator( $this->db->prepare( "select * from " . $this->db->quoteIdentifier( "PO_test" ) ),
                                             new ezcPersistentObjectDefinition );
        $this->assertEquals( null, $it->current() );
        $this->assertEquals( null, $it->next() );
        $this->assertEquals( false, $it->valid() );
    }

    public function testValidIsInvalidBeforeRewind()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->gt( $this->db->quoteIdentifier( 'id' ), 2 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $this->assertEquals( false, $it->valid() );
    }

    public function testValidIsValidWhenNextWithoutRewind()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->gt( $this->db->quoteIdentifier( 'id' ), 2 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $it->next();
        $this->assertEquals( true, $it->valid() );
    }

    public function testValidIsValidWhenRewindIsCalled()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->gt( $this->db->quoteIdentifier( 'id' ), 2 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $it->rewind();
        $this->assertEquals( true, $it->valid() );
    }

    public function testValidNoResults()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->gt( $this->db->quoteIdentifier( 'id' ), 42 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $it->rewind();
        $this->assertEquals( null, $it->next() );
        $this->assertEquals( false, $it->valid() );
        // we check two times to make sure that there is no state problems triggering the first time
        $this->assertEquals( null, $it->next() );
        $this->assertEquals( false, $it->valid() );
    }

    public function testValidOneResult()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->eq( $this->db->quoteIdentifier( 'id' ), 3 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $object = $it->next();
        // check that the data is correct
        $this->assertEquals( 'Ukraine', $object->varchar );
        $this->assertEquals( 47732079, (int)$object->integer );
        $this->assertEquals( 603.70, (float)$object->decimal );
        $this->assertEquals( 'Ukraine has a long coastline to the black see.', $object->text );

        // check that there are no more results
        $this->assertEquals( null, $it->next() );
    }

    public function testValidManyResults()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->gt( $this->db->quoteIdentifier( 'id' ), 2 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $object = $it->next();
        // check that the data is correct
        $this->assertEquals( 'Ukraine', $object->varchar );
        $this->assertEquals( 47732079, (int)$object->integer );
        $this->assertEquals( 603.70, (float)$object->decimal );
        $this->assertEquals( 'Ukraine has a long coastline to the black see.', $object->text );

        $object = $it->next();
        $this->assertEquals( 'Germany', $object->varchar );
        $this->assertEquals( 82443000, (int)$object->integer );
        $this->assertEquals( 357.02, (float)$object->decimal );
        $this->assertEquals( 'Home of the lederhosen!.', $object->text );

        // check that there are no more results
        $this->assertEquals( null, $it->next() );
    }

    /*
     * Issue #14473: ezcPersistentFindIterator overwrites last object
    public function testThatOnlyOneObjectIsUsed()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->gt( $this->db->quoteIdentifier( 'id' ), 2 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $object1 = $it->next();
        $object2 = $it->next();
        $this->assertEquals( $object1, $object2 );
    }
    */

    public function testMultipleResultsStateNotOverwritten()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $q->where( $q->expr->gt( $this->db->quoteIdentifier( 'id' ), 2 ) );
        $stmt = $q->prepare();
        $stmt->execute();
        $it = new ezcPersistentFindIterator( $stmt, $this->manager->fetchDefinition( 'PersistentTestObject' ) );

        $last = null;
        foreach ( $it as $current )
        {
            $this->assertNotSame(
                $last,
                $current,
                'Objects are the same instance.'
            );
            $this->assertNotEquals(
                $last,
                $current,
                'Objects have the same content.'
            );
            $last = $current;
        }
    }

    public function testBreakOutOfIterator()
    {
        $q = $this->session->createFindQuery( 'PersistentTestObject' );
        $it = $this->session->findIterator( $q, 'PersistentTestObject' );
        $i = 0;
        foreach ( $it as $object )
        {
            break;
        }
        $it->flush();
        $it = $this->session->find( $q, 'PersistentTestObject' );
    }
}
?>
