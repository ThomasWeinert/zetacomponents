<?php
/**
 * File containing the ezcDocumentOdtListStyleGenerator class.
 *
 * @package Document
 * @version //autogen//
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Class to generate styles for paragraph elements (<h> and <p>).
 *
 * @package Document
 * @access private
 * @version //autogen//
 */
class ezcDocumentOdtListStyleGenerator extends ezcDocumentOdtStyleGenerator
{
    /**
     * Text style generator.
     * 
     * @var ezcDocumentOdtTextStyleGenerator
     */
    protected $textStyleGenerator;

    /**
     * List property generator. 
     * 
     * @var ezcDocumentOdtStyleListPropertyGenerator
     */
    protected $listPropertyGenerator;

    /**
     * List IDs.
     *
     * @var int 
     */
    protected static $id = 0;

    /**
     * Creates a new style genertaor.
     * 
     * @param ezcDocumentOdtPcssConverterManager $styleConverters 
     */
    public function __construct( ezcDocumentOdtPcssConverterManager $styleConverters )
    {
        parent::__construct( $styleConverters );
        $this->textStyleGenerator = new ezcDocumentOdtTextStyleGenerator(
            $styleConverters
        );
        $this->listPropertyGenerator = new ezcDocumentOdtStyleListPropertyGenerator(
            $styleConverters
        );
    }

    /**
     * Returns if the given $odtElement is handled by this generator.
     * 
     * @param DOMElement $odtElement 
     * @return bool
     */
    public function handles( DOMElement $odtElement )
    {
        return (
            $odtElement->localName === 'list' || $odtElement->localName === 'list-item'
        );
    }
    
    /**
     * Creates the styles with $styleAttributes for the given $odtElement.
     * 
     * @param DOMElement $odtElement 
     * @param array(string=>ezcDocumentPcssStyleValue) $styleAttributes 
     */
    public function createStyle( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $odtElement, array $styleAttributes )
    {
        switch ( $odtElement->localName )
        {
            case 'list':
                $this->createListStyle( $styleInfo, $odtElement, $styleAttributes );
                break;
            case 'list-item':
                // $this->createListItemStyle( $styleInfo, $odtElement, $styleAttributes );
        }
    }

    /**
     * Creates a style for the <text:list /> element.
     *
     * Checks if the list is nested in a different list. If this is not the 
     * case, a new list style is generated. Otherwise, the existing list style 
     * is retrieved and a list definition for the corresponding nesting depth 
     * is created.
     * 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @param DOMElement $list 
     * @param array $styleAttributes 
     * @return void
     */
    protected function createListStyle( ezcDocumentOdtStyleInformation $styleInfo, DOMElement $list, array $styleAttributes )
    {
        $baseListDef = $this->getBaseList( $list );

        if ( $baseListDef['list'] === null )
        {
            $listStyle = $this->createNewListStyle( $list, $styleInfo );
            $level = 1;
        }
        else
        {
            $listStyle = $this->retrieveListStyle( $baseListDef['list'], $styleInfo );
            $level = $baseListDef['depth'];
        }

        $this->createListLevelStyle( $styleInfo, $listStyle, $level, $styleAttributes );
    }

    /**
     * Creates a new <text:list-style/> and applies it to the given 
     * $odtElement.
     *
     * This method creates and returns a new list style DOMElement in 
     * $styleInfo for $odtElement and assigns its name to the $odtElement. The 
     * list style can then be filled with list properties of different levels.
     * 
     * @param DOMElement $odtElement 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @return DOMElement
     */
    protected function createNewListStyle( DOMElement $odtElement, ezcDocumentOdtStyleInformation $styleInfo )
    {

        $listStyle = $styleInfo->styleSection->appendChild(
            $styleInfo->styleSection->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'list-style'
            )
        );
        $listStyle->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            'style:name',
            ( $styleName = $this->getUniqueStyleName( 'l' ) )
        );
        
        $odtElement->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'text:style-name',
            $styleName
        );
        // OOO attaches IDs to root lists, so do we.
        $odtElement->setAttributeNS(
            ezcDocumentOdt::NS_XML,
            'xml:id',
            sprintf( "%s%s", 'list', ++self::$id )
        );

        return $listStyle;
    }

    /**
     * Creates the <text:list-level-style-* /> elemnt for $styleAttributes.
     *
     * This method creates a list-level-style in $listStyle for the given list 
     * $level applying $styleAttributes to this list level.
     * 
     * @param mixed $listStyle 
     * @param mixed $level 
     * @param mixed $styleAttributes 
     * @return void
     */
    protected function createListLevelStyle( ezcDocumentOdtStyleInformation $styleInfo, $listStyle, $level, $styleAttributes )
    {
        $styleAttributes['margin']->value['left'] = $this->calculateListMargin(
            $listStyle,
            $level,
            $styleAttributes['margin']->value['left'],
            $styleAttributes['padding']->value['left']
        );

        $listLevelStyle = $listStyle->appendChild(
            $listStyle->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'list-level-style-' . $styleAttributes['list-type']->value
            )
        );

        $listLevelStyle->setAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'level',
            $level
        );

        // Creates the text:style-name attribute with a new style that is 
        // applied to the bullet/numbering.
        $this->textStyleGenerator->createStyle(
            $styleInfo,
            $listLevelStyle,
            $styleAttributes
        );

        // Set by OOO no matter if bullet or number list
        // @TODO: Make styleable
        $listLevelStyle->setAttributeNS(
            ezcDocumentOdt::NS_ODT_STYLE,
            'style:num-suffix',
            '.'
        );

        $this->listPropertyGenerator->createProperty(
            $listLevelStyle,
            $styleAttributes
        );

        if ( $styleAttributes['list-type']->value === 'bullet' )
        {
            $listLevelStyle->setAttributeNS(
                ezcDocumentOdt::NS_ODT_TEXT,
                'text:bullet-char',
                $styleAttributes['list-bullet']->value
            );
        }
        else
        {
            $listLevelStyle->setAttributeNS(
                ezcDocumentOdt::NS_ODT_STYLE,
                'style:num-format',
                $styleAttributes['list-number']->value
            );
        }
    }

    /**
     * Calculates the absolue left margin for the current list level.
     *
     * This methdod extracts the previous list levels margin from $listStyle as 
     * the base for the margin on $level. This margin + the given $margin +
     * $padding are returned as the absolute margin for $level.
     * 
     * @param DOMElement $listStyle 
     * @param int $level 
     * @param int $margin 
     * @param int $padding 
     * @return int
     */
    protected function calculateListMargin( DOMElement $listStyle, $level, $margin, $padding )
    {
        $previousMargin = 0;
        echo "Level $level, margin $margin, padding $padding\n";

        foreach( $listStyle->childNodes as $listStyleChild )
        {
            if ( $listStyleChild->nodeType === XML_ELEMENT_NODE
              && strpos( $listStyleChild->localName, 'list-level-style-' ) === 0
              && $listStyleChild->hasAttributeNS( ezcDocumentOdt::NS_ODT_TEXT, 'level' )
              && $listStyleChild->getAttributeNS( ezcDocumentOdt::NS_ODT_TEXT, 'level' ) == ( $level - 1 )
            )
            {
                $alignementProps = $listStyleChild->getElementsByTagNameNS(
                    ezcDocumentOdt::NS_ODT_STYLE,
                    'list-level-label-alignment'
                );
                if ( $alignementProps->length === 1 )
                {
                    $previousMargin = (int) $alignementProps->item( 0 )->getAttributeNS(
                        ezcDocumentOdt::NS_ODT_FO,
                        'margin-left'
                    );
                }
                break;
            }
        }

        echo "  Previous margin $previousMargin, full margin " . ( $previousMargin + $margin + $padding ) . "\n";

        return $previousMargin + $margin + $padding;
    }

    /**
     * Returns the <text:list-style> DOMElement assigned to $odtList.
     * 
     * @param DOMElement $odtList 
     * @param ezcDocumentOdtStyleInformation $styleInfo 
     * @return DOMElement
     */
    protected function retrieveListStyle( $odtList, ezcDocumentOdtStyleInformation $styleInfo )
    {
        $styleName = $odtList->getAttributeNS(
            ezcDocumentOdt::NS_ODT_TEXT,
            'style-name'
        );

        $xpath = new DOMXpath( $styleInfo->styleSection->ownerDocument );
        $xpath->registerNamespace( ezcDocumentOdt::NS_ODT_TEXT, 'text' );
        $xpath->registerNamespace( ezcDocumentOdt::NS_ODT_STYLE, 'style' );

        $styleList = $xpath->query(
            "text:list-style[@style:name='{$styleName}']",
            $styleInfo->styleSection
        );
        
        if ( $styleList->length !== 1 )
        {
            throw new RuntimeException(
                "Inconsistent style section. Found {$styleList->length} list styles with name '{$styleName}'"
            );
        }

        return $styleList->item( 0 );
    }

    /**
     * Returns the parent <text:list/> element or null.
     *
     * This method returns the parent <text:list/> element for the given $list and the nesting depth of $list, 
     * if it is nested in another list. The returned structure is:
     *
     * <code>
     * <?php
     *  array(
     *      'base'  => <DOMElement|null>,
     *      'depth' => <int>
     *  );
     * ?>
     * </code>
     * 
     * @param DOMElement $list 
     * @return array
     */
    protected function getBaseList( DOMElement $list, $depth = 1 )
    {
        $parent = $list->parentNode;
        if ( $parent === null || $parent->nodeType === XML_DOCUMENT_NODE )
        {
            return array(
                'list'   => null,
                'depth' => $depth
            );
        }
        if ( $parent->nodeType === XML_ELEMENT_NODE && $parent->localName === 'list' )
        {
            ++$depth;
            if ( $parent->hasAttributeNs( ezcDocumentOdt::NS_ODT_TEXT, 'style-name' ) )
            {
                return array(
                    'list' => $parent,
                    'depth' => $depth
                );
            }
        }
        return $this->getBaseList( $parent, $depth );
    }
}

?>
