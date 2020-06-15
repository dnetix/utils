<?php

namespace Dnetix\Presenters;

/**
 * Class Presenter.
 * @author Diego Calle
 */
abstract class Presenter
{
    /**
     * Instance of the class to present.
     * @var
     */
    protected $entity;

    public function __construct(&$entity)
    {
        $this->entity = &$entity;
    }

    /**
     * Magic method to allow using the original object in case that a presenter property has not been
     * declared.
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->{$property}();
        }
        return $this->entity->{$property};
    }

    /**
     * Magic method to allow using the original object in case that a presenter method has not been
     * declared.
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->entity->{$name}($arguments);
    }
}
