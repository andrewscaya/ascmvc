<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.3.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing;

/**
 * Class AggregateImmutableValueObject
 *
 * @package Ascmvc\EventSourcing
 */
class AggregateImmutableValueObject implements \Serializable
{
    /**
     * Contains the AggregateValue object's properties.
     * @var array
     */
    protected $properties = [];

    /**
     * AggregateValue constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = null)
    {
        if (is_null($properties)) {
            $this->properties = [];
        } else {
            $this->properties = $properties;
        }
    }

    /**
     * Gets the AggregateValue object's properties.
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Serializes this object.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->properties);
    }

    /**
     * Unserializes the properties of this object.
     *
     * @param string $serialized
     * @return AggregateImmutableValueObject|bool
     */
    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);

        if (is_array($unserialized)) {
            return new self($unserialized);
        } else {
            return false;
        }
    }

    /**
     * Hydrates the object's properties to an array.
     *
     * @return array
     */
    public function hydrateToArray()
    {
        return $this->properties;
    }

    /**
     * Allows for automatic array to string conversion of the object's properties.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->serialize();
    }
}
