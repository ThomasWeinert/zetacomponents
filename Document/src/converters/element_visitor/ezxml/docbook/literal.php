<?php

/**
 * File containing the ezcDocumentElementVisitorConverter class
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit eZXml literals
 *
 * All literal elements are considered literal blocks, and though are moved
 * outside of the paragraph.
 * 
 * @package Document
 * @version //autogen//
 */
class ezcDocumentEzXmlToDocbookLiteralHandler extends ezcDocumentElementVisitorHandler
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
        $element = $root->ownerDocument->createElement( 'literallayout' );
        $root->parentNode->appendChild( $element );

        // If there are any siblings, put them into a new paragraph node,
        // "below" the list node.
        if ( $node->nextSibling )
        {
            $newParagraph = $node->ownerDocument->createElement( 'paragraph' );
            
            do {
                $newParagraph->appendChild( $node->nextSibling->cloneNode( true ) );
                $node->parentNode->removeChild( $node->nextSibling );
            } while ( $node->nextSibling );

            $node->parentNode->parentNode->appendChild( $newParagraph );
        }

        // Recurse
        $converter->visitChildren( $node, $element );
        return $root;
    }
}

?>