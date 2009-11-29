<?php
/**
 * File containing the ezcDocumentDocbookToOdtAnchorHandler class.
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Visit anchors.
 *
 * Visit docbook anchors and transform them into ODT <text:reference-mark/>.
 *
 * @package Document
 * @version //autogen//
 * @access private
 */
class ezcDocumentDocbookToOdtAnchorHandler extends ezcDocumentDocbookToOdtBaseHandler
{
    /**
     * Handle a node
     *
     * Handle / transform a given node, and return the result of the
     * conversion.
     *
     * @param ezcDocumentElementVisitorConverter $converter
     * @param DOMElement $node
     * @param mixed $root
     * @return mixed
     */
    public function handle( ezcDocumentElementVisitorConverter $converter, DOMElement $node, $root )
    {
        $this->createRefMark(
            $node,
            $root
        );

        return $root;
    }

    /**
     * Creates a ref-mark as the first element of the given $odtElement, based 
     * on the ID attribute of the given $docbookElement.
     * 
     * @param DOMElement $docbookElement 
     * @param DOMElement $odtElement 
     */
    protected function createRefMark( DOMElement $docbookElement, DOMElement $odtElement )
    {
        // Work around for DocBook inconsistency in using ID or id. id 
        // would  be correct, if one follows the specs here…
        if ( $docbookElement->hasAttribute( 'ID' ) || $docbookElement->hasAttribute( 'id' ) )
        {
            $refMark = $odtElement->insertBefore(
                $odtElement->ownerDocument->createElementNS(
                    ezcDocumentOdt::NS_ODT_TEXT,
                    'text:reference-mark'
                ),
                $odtElement->firstChild
            );
            $refMark->setAttributeNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:name',
                ( $docbookElement->hasAttribute( 'ID' ) ? $docbookElement->getAttribute( 'ID' ) : $docbookElement->getAttribute( 'id' ) )
            );
        }
    }
}

?>