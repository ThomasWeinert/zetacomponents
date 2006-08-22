<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Mail
 */

/**
 * A container to store a mail address in RFC822 format.
 *
 * The class ezcMailTools contains methods for transformation between several
 * formats.
 *
 * @package Mail
 * @version //autogentag//
 * @mainclass
 */
class ezcMailAddress extends ezcBaseStruct
{
    /**
     * The name of the recipient (optional).
     *
     * @var string
     */
    public $name;

    /**
     * The email address of the recipient.
     *
     * @var string
     */
    public $email;

    /**
     * The character set used in the $name property.
     *
     * The characterset defaults to us-ascii.
     */
    public $charset;

    /**
     * Constructs a new ezcMailAddress with the mail address $email and the optional name $name.
     *
     * @param string $email
     * @param string $name
     */
    public function __construct( $email, $name = '', $charset = 'us-ascii' )
    {
        $this->name = $name;
        $this->email = $email;
        $this->charset = $charset;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed)
     * @return ezcMailAddress
     */
    static public function __set_state( array $array )
    {
        return new ezcMailAddress( $array['email'], $array['name'] );
    }
}
?>
