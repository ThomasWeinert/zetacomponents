<?php
/**
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Translation
 */
/**
 * Interface for Translation backends.
 *
 * This interface describes the methods that a Translation backend should
 * implement.
 *
 * Example:
 * @see ezcTranslationTsBackend for an example.
 *
 * @package Translation
 */
interface ezcTranslationBackend
{
    /**
     * Sets the backend specific $configurationData.
     *
     * $configurationData should be a simple associative array in the
     * form: array('option_name'=>'option_value').
     *
     * Each implementor must document the options that it accepts and throw an
     * {@link ezcBaseConfigException} with the
     * {@link ezcBaseConfigException::UNKNOWN_CONFIG_SETTING} type if an option
     * is not supported.
     *
     * @param mixed $configurationData
     * @return void
     */
    public function setOptions( array $configurationData );

    /**
     * Returns an array with translation data for the context $context and the locale
     * $locale.
     *
     * This method returns an array describing the map used for translation of text.
     * @see ezcTranslation::$translationMap for the format.
     *
     * @throws TranslationException when a context is not available.
     * @param string locale
     * @param string context
     * @return array
     */
    public function getContext( $locale, $context );
}
?>
