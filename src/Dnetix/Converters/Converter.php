<?php


namespace Dnetix\Converters;


abstract class Converter
{

    /**
     * Handles the "to" call that performs the conversion
     * @param $name
     * @param $arguments
     * @return
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if(preg_match("/to([\w\d]+)/", $name, $matches)){
            $toProvider = __NAMESPACE__ . '\Providers\\' .ucfirst($this->identifier() . 'To' . $matches[1]);
            if(class_exists($toProvider)){
                $provider = new $toProvider;
                return call_user_func_array([$provider, 'convertValue'], [$this->value()]);
            }
            throw new \Exception("No provider to perform that conversion");
        }
        throw new \Exception("Call to undefined method " . $name . " on " . get_class($this));
    }

    /**
     * Returns the value in the converter to convert
     * @return mixed
     */
    public abstract function value();

    /**
     * Returns the identifier for the converter
     * @return string
     */
    public abstract function identifier();

}