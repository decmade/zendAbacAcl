<?php

namespace DoctrineORMModule\Proxy\__CG__\Acl\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class User extends \Acl\Entity\User implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = [];



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return ['__isInitialized__', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'identity', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'credential', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'status', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'attributes', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'sessions'];
        }

        return ['__isInitialized__', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'identity', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'credential', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'status', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'attributes', '' . "\0" . 'Acl\\Entity\\User' . "\0" . 'sessions'];
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (User $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', []);
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', []);
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getIdentity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdentity', []);

        return parent::getIdentity();
    }

    /**
     * {@inheritDoc}
     */
    public function setIdentity($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setIdentity', [$value]);

        return parent::setIdentity($value);
    }

    /**
     * {@inheritDoc}
     */
    public function checkCredential($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'checkCredential', [$value]);

        return parent::checkCredential($value);
    }

    /**
     * {@inheritDoc}
     */
    public function setCredential($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCredential', [$value]);

        return parent::setCredential($value);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', []);

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($value)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', [$value]);

        return parent::setStatus($value);
    }

    /**
     * {@inheritDoc}
     */
    public function addAttribute(\Acl\Entity\Attribute $attribute)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addAttribute', [$attribute]);

        return parent::addAttribute($attribute);
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAttributes', []);

        return parent::getAttributes();
    }

    /**
     * {@inheritDoc}
     */
    public function addSession(\Acl\Entity\Session $session)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addSession', [$session]);

        return parent::addSession($session);
    }

    /**
     * {@inheritDoc}
     */
    public function getSessions()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getSessions', []);

        return parent::getSessions();
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', []);

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getAdded()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAdded', []);

        return parent::getAdded();
    }

    /**
     * {@inheritDoc}
     */
    public function getRemoved()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getRemoved', []);

        return parent::getRemoved();
    }

    /**
     * {@inheritDoc}
     */
    public function setRemoved()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setRemoved', []);

        return parent::setRemoved();
    }

    /**
     * {@inheritDoc}
     */
    public function clearRemoved()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'clearRemoved', []);

        return parent::clearRemoved();
    }

}
