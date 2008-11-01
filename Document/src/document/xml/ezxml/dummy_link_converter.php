<?php
/**
 * File containing the abstract ezcDocumentEzXmlLinkProvider class
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class for conversions of links, given as natural URLs into the eZXml storage
 * format, which may result in urlIds, nodeIds, or similar.
 *
 * @package Document
 * @version //autogen//
 */
class ezcDocumentEzXmlDummyLinkConverter extends ezcDocumentEzXmlLinkConverter
{
    /**
     * Get URL properties
     *
     * Return an array of the attributes, which should be set for the link.
     *
     * @param string $url 
     * @return array
     */
    public function getUrlProperties( $url )
    {
        return array(
            'url_id' => 23,
        );
    }
}

?>
