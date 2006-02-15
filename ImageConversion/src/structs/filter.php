<?php
/**
 * File containing the ezcImageFilter struct.
 *
 * @package ImageConversion
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct to store information about a filter operation.
 *
 * The struct contains the {@link self::name name} of the filter to use and
 * which {@link self::options options} to use for it.
 *
 * @see ezcImageTransformation
 *
 * @package ImageConversion
 */
class ezcImageFilter
{
    /**
     * Name of filter operation to use.
     *
     * @see ezcImageEffectFilters
     * @see ezcImageGeometryFilters
     * @see ezcImageColorspaceFilters
     *
     * @var string
     */
    public $name;

    /**
     * Associative array of options for the filter operation.
     * The array key is the option name and the array entry is the value for
     * the option.
     * Consult each filter operation to see which names and values to use.
     *
     * @see ezcImageEffectFilters
     * @see ezcImageGeometryFilters
     * @see ezcImageColorspaceFilters
     *
     * @var array(string=>mixed)
     */
    public $options;

    /**
     * Initialize with the filter name and options.
     *
     * @see ezcImageFilter::$name
     * @see ezcImageFilter::$options
     *
     * @param array $name    Name of filter operation.
     * @param array $options Associative array of options for filter operation.
     */
    public function __construct( $name, array $options = array() )
    {
        $this->name    = $name;
        $this->options = $options;
    }
}
?>
