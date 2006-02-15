<?php
/**
 * File containing the ezcMailTools class
 *
 * @package Mail
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class contains static convenience methods for composing addresses
 * and ensuring correct line-breaks in the mail.
 *
 * @package Mail
 * @version //autogen//
 */
class ezcMailTools
{
    /**
     * Holds the unique ID's.
     *
     * @var int
     */
    private static $idCounter = 0;

    /**
     * The characters to use for line-breaks in the mail.
     *
     * The default is \r\n which is the value specified in RFC822.
     *
     * @var string
     */
    private static $lineBreak = "\r\n";

    /**
     * Returns ezcMailAddress $item as a RFC822 compliant address string.
     *
     * Example:
     * <code>
     * composeEmailAddress( new ezcMailAddress( 'sender@example.com', 'John Doe' ) );
     * </code>
     *
     * Returns:
     * <pre>
     * John Doe <sender@example.com>
     * </pre>
     *
     * @param ezcMailAddress $item
     * @return string
     */
    public static function composeEmailAddress( ezcMailAddress $item )
    {
        if ( $item->name !== '' )
        {
            if ( $item->charset !== 'us-ascii' )
            {
                $preferences = array(
                    'input-charset' => $item->charset,
                    'output-charset' => $item->charset,
                    'scheme' => 'B',
                    'line-break-chars' => ezcMailTools::lineBreak()
                );
                $name = iconv_mime_encode( 'dummy', $item->name, $preferences );
                $name = substr( $name, 7 ); // "dummy: " + 1
                $text = $name . ' <' . $item->email . '>';
            }
            else
            {
                $text = $item->name . ' <' . $item->email . '>';
            }
        }
        else
        {
            $text = $item->email;
        }
        return $text;
    }

    /**
     * Returns the array $items consisting of ezcMailAddress objects
     * as one RFC822 compliant address string.
     *
     * @param array(ezcMailAddress) $items
     * @return string
     */
    public static function composeEmailAddresses( array $items )
    {
        $textElements = array();
        foreach ( $items as $item )
        {
            $textElements[] = ezcMailTools::composeEmailAddress( $item );
        }
        return implode( ', ', $textElements );
    }


    /**
     * Returns an unique message ID to be used for a mail message.
     *
     * The hostname $hostname will be added to the unique ID as required by RFC822.
     * If an e-mail address is provided instead, the hostname is extracted and used.
     *
     * The formula to generate the message ID is: [time_and_date].[process_id].[counter]
     *
     * @param string $hostname
     * @return string
     */
    public static function generateMessageId( $hostname )
    {
        if ( strpos( $hostname, '@' ) !== false )
        {
            $hostname = strstr( $hostname, '@' );
        }
        else
        {
            $hostname = '@' . $hostname;
        }
        return date( 'YmdGHjs' ) . '.' . getmypid() . '.' . self::$idCounter++ . $hostname;
    }

    /**
     * Returns an unique ID to be used for Content-ID headers.
     *
     * The part $partName is default set to "part". Another value can be used to provide,
     * for example, a file name of a part.
     *
     * The formula used is [$partName]."@".[time].[counter]
     *
     * @param  string $partName
     * @return string
     */
    public static function generateContentId( $partName = "part" )
    {
        return $partName . '@' .  date( 'Hjs' ) . self::$idCounter++;
    }

    /**
     * Sets the endLine $character(s) to use when generating mail.
     * The default is to use "\r\n" as specified by RFC 2045.
     *
     * @param string $characters
     * @return void
     */
    public static function setLineBreak( $characters )
    {
        self::$lineBreak = $characters;
    }

    /**
     * Returns one endLine character.
     *
     * The default is to use "\n\r" as specified by RFC 2045.
     *
     * @return string
     */
    public static function lineBreak()
    {
        // Note, this function does deliberately not
        // have a $count parameter because of speed issues.
        return self::$lineBreak;
    }
}
?>
