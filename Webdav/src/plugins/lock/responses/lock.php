<?php
/**
 * File containing the ezcWebdavLockResponse class.
 *
 * @package Webdav
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class generated by the lock plugin to respond to LOCK requests.
 *
 * If a {@link ezcWebdavLockPlugin} receives an instance of {@link
 * ezcWebdavLockRequest} it might react with an instance of {@link
 * ezcWebdavLockResponse} or with producing an error.
 *
 * @property ezcWebdavLockDiscoveryProperty $lockDiscovery
 *           Lock discovery property to the ressource targeted by the LOCK
 *           request, including the newly created or updated active lock part.
 *
 * @version //autogentag//
 * @package Webdav
 */
class ezcWebdavLockResponse extends ezcWebdavResponse
{
    /**
     * Creates a new response object.
     *
     * Creates a new LOCK response object using the given $lockDiscovery
     * property. If the $lockToken parameter is not null, the response will
     * have the Lock-Token header set, which must not occur for refreshing of
     * locks, but must occur for new locks.
     * 
     * @param ezcWebdavLockDiscoveryProperty $lockDiscovery 
     * @param string $lockToken
     */
    public function __construct(
        ezcWebdavLockDiscoveryProperty $lockDiscovery,
        $status = ezcWebdavResponse::STATUS_200,
        $lockToken = null
    )
    {
        parent::__construct( $status );
        $this->lockDiscovery = $lockDiscovery;
        
        if ( $lockToken !== null )
        {
            $this->setHeader( 'Lock-Token', $lockToken );
        }
    }

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'lockDiscovery':
                if ( ( ! $propertyValue instanceof ezcWebdavLockDiscoveryProperty ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcWebdavLockDiscoveryProperty' );
                }
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }

        $this->properties[$propertyName] = $propertyValue;
    }
}

?>