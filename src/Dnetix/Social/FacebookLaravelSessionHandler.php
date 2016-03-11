<?php
namespace Dnetix\Social;

use Facebook\PersistentData\PersistentDataInterface;

class FacebookLaravelSessionHandler implements PersistentDataInterface {

    private $handler;
    protected $sessionPrefix = 'FBRLH_';

    public function __construct($handler) {
        $this->handler = $handler;
    }

    /**
     * Get a value from a persistent data store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key) {
        return $this->handler->get($this->sessionPrefix . $key);
    }

    /**
     * Set a value in the persistent data store.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value) {
        $this->handler->put($this->sessionPrefix . $key, $value);
    }

}