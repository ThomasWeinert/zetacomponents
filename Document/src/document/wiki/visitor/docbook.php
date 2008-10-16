<?php
/**
 * File containing the ezcDocumentWikiDocbookVisitor class
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Docbook visitor for the Wiki AST.
 * 
 * @package Document
 * @version //autogen//
 */
class ezcDocumentWikiDocbookVisitor extends ezcDocumentWikiVisitor
{
    /**
     * Mapping of class names to internal visitors for the respective nodes.
     * 
     * @var array
     */
    protected $complexVisitMapping = array(
        'ezcDocumentWikiTextNode'      => 'visitText',
        'ezcDocumentWikiBoldNode'      => 'visitEmphasisMarkup',
        'ezcDocumentWikiItalicNode'    => 'visitEmphasisMarkup',
        'ezcDocumentWikiUnderlineNode' => 'visitEmphasisMarkup',
        'ezcDocumentWikiTitleNode'     => 'visitTitle',

        'ezcDocumentWikiDeletedNode'   => 'visitChildren',
/*
        'ezcDocumentWikiLiteralNode'               => 'visitText',
        'ezcDocumentWikiExternalReferenceNode'     => 'visitExternalReference',
        'ezcDocumentWikiReferenceNode'             => 'visitInternalFootnoteReference',
        'ezcDocumentWikiAnonymousLinkNode'         => 'visitAnonymousReference',
        'ezcDocumentWikiMarkupSubstitutionNode'    => 'visitSubstitutionReference',
        'ezcDocumentWikiMarkupInterpretedTextNode' => 'visitChildren',
        'ezcDocumentWikiMarkupEmphasisNode'        => 'visitEmphasisMarkup',
        'ezcDocumentWikiTargetNode'                => 'visitInlineTarget',
        'ezcDocumentWikiBlockquoteNode'            => 'visitBlockquote',
        'ezcDocumentWikiEnumeratedListListNode'    => 'visitEnumeratedList',
        'ezcDocumentWikiDefinitionListNode'        => 'visitDefinitionListItem',
        'ezcDocumentWikiTableNode'                 => 'visitTable',
        'ezcDocumentWikiTableCellNode'             => 'visitTableCell',
        'ezcDocumentWikiFieldListNode'             => 'visitFieldListItem',
        'ezcDocumentWikiLineBlockNode'             => 'visitLineBlock',
        'ezcDocumentWikiLineBlockLineNode'         => 'visitChildren',
        'ezcDocumentWikiDirectiveNode'             => 'visitDirective',
        // */
    );

    /**
     * Direct mapping of AST node class names to docbook element names.
     * 
     * @var array
     */
    protected $simpleVisitMapping = array(
        'ezcDocumentWikiParagraphNode'   => 'para',
        'ezcDocumentWikiSectionNode'     => 'section',
        'ezcDocumentWikiInlineQuoteNode' => 'quote',
        'ezcDocumentWikiSuperscriptNode' => 'superscript',
        'ezcDocumentWikiSubscriptNode'   => 'subscript',
        'ezcDocumentWikiMonospaceNode'   => 'literal',
        'ezcDocumentWikiBlockquoteNode'  => 'blockquote',

/*
        'ezcDocumentWikiMarkupInlineLiteralNode' => 'literal',
        'ezcDocumentWikiBulletListListNode'      => 'itemizedlist',
        'ezcDocumentWikiDefinitionListListNode'  => 'variablelist',
        'ezcDocumentWikiBulletListNode'          => 'listitem',
        'ezcDocumentWikiEnumeratedListNode'      => 'listitem',
        'ezcDocumentWikiLiteralBlockNode'        => 'literallayout',
        'ezcDocumentWikiCommentNode'             => 'comment',
        'ezcDocumentWikiTransitionNode'          => 'beginpage',
        'ezcDocumentWikiTableHeadNode'           => 'thead',
        'ezcDocumentWikiTableBodyNode'           => 'tbody',
        'ezcDocumentWikiTableRowNode'            => 'row',
        // */
    );

    /**
     * Array with nodes, which can be ignored during the transformation
     * process, they only provide additional information during preprocessing.
     * 
     * @var array
     */
    protected $skipNodes = array(
        'ezcDocumentWikiNamedReferenceNode',
        'ezcDocumentWikiAnonymousReferenceNode',
        'ezcDocumentWikiSubstitutionNode',
        'ezcDocumentWikiFootnoteNode',
    );

    /**
     * DOM document
     * 
     * @var DOMDocument
     */
    protected $document;

    /**
     * Docarate Wiki AST
     *
     * Visit the Wiki abstract syntax tree.
     * 
     * @param ezcDocumentWikiDocumentNode $ast 
     * @return mixed
     */
    public function visit( ezcDocumentWikiDocumentNode $ast )
    {
        parent::visit( $ast );

        // Create article from AST
        $imp = new DOMImplementation();
        $dtd = $imp->createDocumentType( 'article', '-//OASIS//DTD DocBook XML V4.5//EN', 'http://www.oasis-open.org/docbook/xml/4.5/docbookx.dtd' );
        $this->document = $imp->createDocument( 'http://docbook.org/ns/docbook', '', $dtd );
        $this->document->formatOutput = true;

//        $root = $this->document->createElement( 'article' );
        $root = $this->document->createElementNs( 'http://docbook.org/ns/docbook', 'article' );
        $this->document->appendChild( $root );

        // Visit all childs of the AST root node.
        foreach ( $ast->nodes as $node )
        {
            $this->visitNode( $root, $node );
        }

        return $this->document;
    }

    /**
     * Visit single AST node
     *
     * Visit a single AST node, may be called for each node found anywhere
     * as child. The current position in the DOMDocument is passed by a
     * reference to the current DOMNode, which is operated on.
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitNode( DOMNode $root, ezcDocumentWikiNode $node )
    {
        // Iterate over available visitors and use them to visit the nodes.
        foreach ( $this->complexVisitMapping as $class => $method )
        {
            if ( $node instanceof $class )
            {
                return $this->$method( $root, $node );
            }
        }

        // Check if we have a simple class to element name mapping
        foreach ( $this->simpleVisitMapping as $class => $elementName )
        {
            if ( $node instanceof $class )
            {
                $element = $this->document->createElement( $elementName );
                $root->appendChild( $element );

                foreach ( $node->nodes as $child )
                {
                    $this->visitNode( $element, $child );
                }

                return;
            }
        }

        // Check if you should just ignore the node for rendering
        foreach ( $this->skipNodes as $class )
        {
            if ( $node instanceof $class )
            {
                return;
            }
        }

        // We could not find any valid visitor.
        throw new ezcDocumentMissingVisitorException( get_class( $node ) );
    }

    /**
     * Visit text node
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitText( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $root->appendChild(
            new DOMText( $node->token->content )
        );
    }

    /**
     * Visit children
     *
     * Just recurse into node and visit its children, ignoring the actual
     * node.
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitChildren( DOMNode $root, ezcDocumentWikiNode $node )
    {
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }
    }

    /**
     * Visit emphasis markup
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitEmphasisMarkup( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $markup = $this->document->createElement( 'emphasis' );

        if ( $node instanceof ezcDocumentWikiBoldNode )
        {
            $markup->setAttribute( 'role', 'strong' );
        }
        $root->appendChild( $markup );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $markup, $child );
        }
    }

    /**
     * Visit section titles
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitTitle( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $title = $this->document->createElement( 'title' );
        $root->appendChild( $title );

        // Add id for internal references.

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $title, $child );
        }
    }

    /**
     * Visit external reference node
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitExternalReference( DOMNode $root, ezcDocumentWikiNode $node )
    {
        if ( $node->target !== false )
        {
            $link = $this->document->createElement( 'ulink' );
            $link->setAttribute( 'url', htmlspecialchars( $node->target ) );
            $root->appendChild( $link );
        }
        elseif ( $target = $this->getNamedExternalReference( $this->nodeToString( $node ) ) )
        {
            $link = $this->document->createElement( 'ulink' );
            $link->setAttribute( 'url', htmlspecialchars( $target ) );
            $root->appendChild( $link );
        }
        else
        {
            $target = $this->hasReferenceTarget( $this->nodeToString( $node ), $node );

            $link = $this->document->createElement( 'link' );
            $link->setAttribute( 'linked', htmlspecialchars( $target ) );
            $root->appendChild( $link );
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $link, $child );
        }
    }

    /**
     * Visit internal reference node
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitInternalFootnoteReference( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $identifier = $this->tokenListToString( $node->name );
        $target = $this->hasFootnoteTarget( $identifier, $node );

        switch ( $node->footnoteType )
        {
            case ezcDocumentWikiFootnoteNode::CITATION:
                // This is a citation reference footnote, which should be
                // visited differently from normal footnotes.
                $this->visitCitation( $root, $target );
                break;

            default:
                // The displayed label of a footnote may not be specified in
                // docbook, so we just add the footnote node.
                $this->visitFootnote( $root, $target );
        }
    }

    /**
     * Visit anonomyous reference node
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitAnonymousReference( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $target = $node->target !== false ? $node->target : $this->getAnonymousReferenceTarget();

        $link = $this->document->createElement( 'ulink' );
        $link->setAttribute( 'url', htmlspecialchars( $target ) );
        $root->appendChild( $link );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $link, $child );
        }
    }

    /**
     * Visit substitution reference node
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitSubstitutionReference( DOMNode $root, ezcDocumentWikiNode $node )
    {
        if ( ( $substitution = $this->substitute( $this->nodeToString( $node ) ) ) !== null )
        {
            foreach( $substitution as $child )
            {
                $this->visitNode( $root, $child );
            }
        }
    }

    /**
     * Visit inline target node
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitInlineTarget( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $link = $this->document->createElement( 'anchor' );
        $link->setAttribute( 'id', $this->calculateId( $this->nodeToString( $node ) ) );
        $root->appendChild( $link );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $root, $child );
        }
    }

    /**
     * Visit citation
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitCitation( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $footnote = $this->document->createElement( 'citation' );
        $root->appendChild( $footnote );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $footnote, $child );
        }
    }

    /**
     * Visit footnote
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitFootnote( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $footnote = $this->document->createElement( 'footnote' );
        $root->appendChild( $footnote );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $footnote, $child );
        }
    }

    /**
     * Visit blockquotes
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitBlockquote( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $quote = $this->document->createElement( 'blockquote' );
        $root->appendChild( $quote );

        // Add blockquote attribution
        if ( !empty( $node->annotation ) )
        {
            $attribution = $this->document->createElement( 'attribution' );
            $quote->appendChild( $attribution );
            $this->visitNode( $attribution, $node->annotation->nodes );
        }

        // Decoratre blockquote contents
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $quote, $child );
        }
    }

    /**
     * Visit enumerated lists
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitEnumeratedList( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $list = $this->document->createElement( 'orderedlist' );

        // Detect enumeration type
        switch ( true )
        {
            case is_numeric( $node->token->content ):
                $list->setAttribute( 'numeration', 'arabic' );
                break;

            case preg_match( '(^m{0,4}d?c{0,3}l?x{0,3}v{0,3}i{0,3}v?x?l?c?d?m?$)', $node->token->content ):
                $list->setAttribute( 'numeration', 'lowerroman' );
                break;

            case preg_match( '(^M{0,4}D?C{0,3}L?X{0,3}V{0,3}I{0,3}V?X?L?C?D?M?$)', $node->token->content ):
                $list->setAttribute( 'numeration', 'upperroman' );
                break;

            case preg_match( '(^[a-z]$)', $node->token->content ):
                $list->setAttribute( 'numeration', 'loweralpha' );
                break;

            case preg_match( '(^[A-Z]$)', $node->token->content ):
                $list->setAttribute( 'numeration', 'upperalpha' );
                break;
        }

        $root->appendChild( $list );

        // Visit list contents
        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $list, $child );
        }
    }

    /**
     * Visit definition list item
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitDefinitionListItem( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $item = $this->document->createElement( 'varlistentry' );
        $root->appendChild( $item );
    
        $term = $this->document->createElement( 'term', htmlspecialchars( $this->tokenListToString( $node->name ) ) );
        $item->appendChild( $term );

        $definition = $this->document->createElement( 'listitem' );
        $item->appendChild( $definition );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $definition, $child );
        }
    }

    /**
     * Visit line block
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitLineBlock( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $para = $this->document->createElement( 'literallayout' );
        $para->setAttribute( 'class', 'Normal' );
        $root->appendChild( $para );

        // Visit lines
        foreach ( $node->nodes as $child )
        {
            foreach ( $child->nodes as $literal )
            {
                $para->appendChild( new DOMText(
                    ( $literal->token->type !== ezcDocumentWikiToken::NEWLINE ) ? $literal->token->content : ' '
                ) );
            }
            $para->appendChild( new DOMText( "\n" ) );
        }
    }

    /**
     * Visit table
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitTable( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $table = $this->document->createElement( 'table' );
        $root->appendChild( $table );

        $group = $this->document->createElement( 'tgroup' );
        $table->appendChild( $group );

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $group, $child );
        }
    }

    /**
     * Visit table cell
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitTableCell( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $cell = $this->document->createElement( 'entry' );
        $root->appendChild( $cell );

        // @TODO: Colspans may be generated by spanspecs, like shown here:
        // http://www.oasis-open.org/docbook/documentation/reference/html/table.html
        if ( $node->rowspan > 1 )
        {
            $cell->setAttribute( 'morerows', $node->rowspan - 1 );
        }

        foreach ( $node->nodes as $child )
        {
            $this->visitNode( $cell, $child );
        }
    }

    /**
     * Visit field list item
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitFieldListItem( DOMNode $root, ezcDocumentWikiNode $node )
    {
        // Get sectioninfo node, to add the stuff there.
        $secInfo = $root->getElementsByTagName( 'sectioninfo' )->item( 0 );

        $fieldListItemMapping = array(
            'authors'     => 'authors',
            'description' => 'abstract',
            'copyright'   => 'copyright',
            'version'     => 'releaseinfo',
            'date'        => 'date',
            'author'      => 'author',
        );

        $fieldName = strtolower( trim( $this->tokenListToString( $node->name ) ) );
        if ( !isset( $fieldListItemMapping[$fieldName] ) )
        {
            return $this->triggerError(
                E_NOTICE, "Unhandeled field list type '{$fieldName}'.",
                null, $node->token->line, $node->token->position
            );
        }

        $item = $this->document->createElement(
            $fieldListItemMapping[$fieldName],
            htmlspecialchars( $this->nodeToString( $node ) )
        );
        $secInfo->appendChild( $item );
    }

    /**
     * Visit directive
     * 
     * @param DOMNode $root 
     * @param ezcDocumentWikiNode $node 
     * @return void
     */
    protected function visitDirective( DOMNode $root, ezcDocumentWikiNode $node )
    {
        $handlerClass = $this->wiki->getDirectiveHandler( $node->identifier );
        $directiveHandler = new $handlerClass( $this->ast, $this->path, $node );
        $directiveHandler->toDocbook( $this->document, $root );
    }
}

?>
