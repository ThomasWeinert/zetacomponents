<?php
/**
 * File containing the class ezcWebdavDisplayInformation.
 *
 * @package Webdav
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Display information.
 *
 * Used by {@link ezcWebdavTransport} to transport information on displaying a
 * response to the browser.
 *
 * @version //autogentag//
 * @package Webdav
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
class ezcWebdavDisplayInformation
{
    
    /**
     * Creates a new struct.
     * 
     * @param ezcWebdavResponse $response 
     * @param DOMDocument $body 
     * @return void
     */
    public function __construct( ezcWebdavResponse $response, DOMDocument $body = null )
    {
        $this->response = $response;
        $this->body     = $body;
    }

    /**
     * Response object to extract headers from.
     * 
     * @var ezcWebdavResponse
     */
    public $response;

    /**
     * DOMDocument representing the response body.
     * Should be empty, if no body should be send.
     * 
     * @var DOMDocument|null
     */
    public $body;
}

?>
