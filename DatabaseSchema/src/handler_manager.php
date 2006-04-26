<?php
/**
 * File containing the ezcDbSchemaHandlerManager class.
 *
 * @package DatabaseSchema
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Deals with schema handlers for a ezcDbSchema object.
 *
 * Determines which handlers to use for the specified storage type.
 *
 * @package DatabaseSchema
 * @version //autogentag//
 */
class ezcDbSchemaHandlerManager
{
    /**
     * Set of standard read handlers.
     *
     * The property is an array where the key is the name of the format and the
     * value the classname that implements the read handler.
     *
     * @var array(string=>string)
     */
    static public $readHandlers = array(
        'array' => 'ezcDbSchemaPhpArrayReader',
        'mysql' => 'ezcDbSchemaMysqlReader',
//        'oracle' => 'ezcDbSchemaOracleReader',
//        'pgsql' => 'ezcDbSchemaPgsqlReader',
//        'sqlite' => 'ezcDbSchemaSqliteReader',
        'xml' => 'ezcDbSchemaXmlReader',
        'pos' => 'ezcDbSchemaPosReader',
    );

    /**
     * Set of standard write handlers.
     *
     * The property is an array where the key is the name of the format and the
     * value the classname that implements the write handler.
     *
     * @var array(string=>string)
     */
    static public $writeHandlers = array(
        'array' => 'ezcDbSchemaPhpArrayWriter',
        'mysql' => 'ezcDbSchemaMysqlWriter',
//        'oracle' => 'ezcDbSchemaOracleWriter',
//        'pgsql' => 'ezcDbSchemaPgsqlWriter',
//        'sqlite' => 'ezcDbSchemaSqliteWriter',
        'xml' => 'ezcDbSchemaXmlWriter',
        'pos' => 'ezcDbSchemaPosWriter',
    );

    /**
     * Set of standard difference read handlers.
     *
     * The property is an array where the key is the name of the format and the
     * value the classname that implements the read handler.
     *
     * @var array(string=>string)
     */
    static public $diffReadHandlers = array(
        'array' => 'ezcDbSchemaPhpArrayReader',
        'xml' => 'ezcDbSchemaXmlReader',
    );

    /**
     * Set of standard difference write handlers.
     *
     * The property is an array where the key is the name of the format and the
     * value the classname that implements the write handler.
     *
     * @var array(string=>string)
     */
    static public $diffWriteHandlers = array(
        'array' => 'ezcDbSchemaPhpArrayWriter',
        'mysql' => 'ezcDbSchemaMysqlWriter',
//        'oracle' => 'ezcDbSchemaOracleWriter',
//        'pgsql' => 'ezcDbSchemaPgsqlWriter',
//        'sqlite' => 'ezcDbSchemaSqliteWriter',
        'xml' => 'ezcDbSchemaXmlWriter',
    );

    /**
     * Returns the class name of the read handler for format $format.
     *
     * @param string $format
     * @return string
     */
    static public function getReaderByFormat( $format )
    {
        if ( !isset( self::$readHandlers[$format] ) )
        {
            throw new ezcDbSchemaUnknownFormatException( $format, 'read' );
        }
        return self::$readHandlers[$format];
    }

    /**
     * Returns the class name of the write handler for format $format.
     *
     * @param string $format
     * @return string
     */
    static public function getWriterByFormat( $format )
    {
        if ( !isset( self::$writeHandlers[$format] ) )
        {
            throw new ezcDbSchemaUnknownFormatException( $format, 'write' );
        }
        return self::$writeHandlers[$format];
    }

    /**
     * Returns the class name of the differences read handler for format $format.
     *
     * @param string $format
     * @return string
     */
    static public function getDiffReaderByFormat( $format )
    {
        if ( !isset( self::$diffReadHandlers[$format] ) )
        {
            throw new ezcDbSchemaUnknownFormatException( $format, 'difference read' );
        }
        return self::$diffReadHandlers[$format];
    }

    /**
     * Returns the class name of the differences write handler for format $format.
     *
     * @param string $format
     * @return string
     */
    static public function getDiffWriterByFormat( $format )
    {
        if ( !isset( self::$diffWriteHandlers[$format] ) )
        {
            throw new ezcDbSchemaUnknownFormatException( $format, 'difference write' );
        }
        return self::$diffWriteHandlers[$format];
    }

    /**
     * Returns list of schema types supported by all known handlers.
     *
     * Goes through the list of known handlers and gathers information of which
     * schema types do they support.
     *
     * @return array
     */
    static public function getSupportedFormats()
    {
        return array_keys( self::$readHandlers ) + array_keys( self::$writeHandlers );
    }

    /**
     * Returns list of schema types supported by all known difference handlers.
     *
     * Goes through the list of known difference handlers and gathers
     * information of which schema types do they support.
     *
     * @return array
     */
    static public function getSupportedDiffFormats()
    {
        return array_keys( array_merge( self::$diffReadHandlers, self::$diffWriteHandlers ) );
    }

    /**
     * Adds the read handler class $readerClass to the manager for $type
     *
     * @throws ezcDbSchemaInvalidReaderClassException if the $readerClass
     *         doesn't exist or doesn't extend the abstract class
     *         ezcDbSchemaReader.
     * @param string $type
     * @param string $readerClass
     */
    static public function addReader( $type, $readerClass )
    {
        // Check if the passed classname actually exists
        if ( !class_exists( $readerClass, true ) )
        {
            throw new ezcDbSchemaInvalidReaderClassException( $readerClass );
        }

        // Check if the passed classname actually implements the interface. We
        // have to do that with reflection here unfortunately
        $interfaceClass = new ReflectionClass( 'ezcDbSchemaReader' );
        $handlerClass = new ReflectionClass( $readerClass );
        if ( !$handlerClass->isSubclassOf( $interfaceClass ) )
        {
            throw new ezcDbSchemaInvalidReaderClassException( $readerClass );
        }
        self::$readHandlers[$type] = $readerClass;
    }

    /**
     * Adds the write handler class $writerClass to the manager for $type
     *
     * @throws ezcDbSchemaInvalidWriterClassException if the $writerClass
     *         doesn't exist or doesn't extend the abstract class
     *         ezcDbSchemaWriter.
     * @param string $type
     * @param string $writerClass
     */
    static public function addWriter( $type, $writerClass )
    {
        // Check if the passed classname actually exists
        if ( !class_exists( $writerClass, true ) )
        {
            throw new ezcDbSchemaInvalidWriterClassException( $writerClass );
        }

        // Check if the passed classname actually implements the interface. We
        // have to do that with reflection here unfortunately
        $interfaceClass = new ReflectionClass( 'ezcDbSchemaWriter' );
        $handlerClass = new ReflectionClass( $writerClass );
        if ( !$handlerClass->isSubclassOf( $interfaceClass ) )
        {
            throw new ezcDbSchemaInvalidWriterClassException( $writerClass );
        }
        self::$writeHandlers[$type] = $writerClass;
    }

    /**
     * Adds the difference read handler class $readerClass to the manager for $type
     *
     * @throws ezcDbSchemaInvalidReaderClassException if the $readerClass
     *         doesn't exist or doesn't extend the abstract class
     *         ezcDbSchemaDiffReader.
     * @param string $type
     * @param string $readerClass
     */
    static public function addDiffReader( $type, $readerClass )
    {
        // Check if the passed classname actually exists
        if ( !class_exists( $readerClass, true ) )
        {
            throw new ezcDbSchemaInvalidDiffReaderClassException( $readerClass );
        }

        // Check if the passed classname actually implements the interface. We
        // have to do that with reflection here unfortunately
        $interfaceClass = new ReflectionClass( 'ezcDbSchemaDiffReader' );
        $handlerClass = new ReflectionClass( $readerClass );
        if ( !$handlerClass->isSubclassOf( $interfaceClass ) )
        {
            throw new ezcDbSchemaInvalidDiffReaderClassException( $readerClass );
        }
        self::$diffReadHandlers[$type] = $readerClass;
    }

    /**
     * Adds the difference write handler class $writerClass to the manager for $type
     *
     * @throws ezcDbSchemaInvalidWriterClassException if the $writerClass
     *         doesn't exist or doesn't extend the abstract class
     *         ezcDbSchemaDiffWriter.
     * @param string $type
     * @param string $writerClass
     */
    static public function addDiffWriter( $type, $writerClass )
    {
        // Check if the passed classname actually exists
        if ( !class_exists( $writerClass, true ) )
        {
            throw new ezcDbSchemaInvalidDiffWriterClassException( $writerClass );
        }

        // Check if the passed classname actually implements the interface. We
        // have to do that with reflection here unfortunately
        $interfaceClass = new ReflectionClass( 'ezcDbSchemaDiffWriter' );
        $handlerClass = new ReflectionClass( $writerClass );
        if ( !$handlerClass->isSubclassOf( $interfaceClass ) )
        {
            throw new ezcDbSchemaInvalidDiffWriterClassException( $writerClass );
        }
        self::$diffWriteHandlers[$type] = $writerClass;
    }
}
?>
