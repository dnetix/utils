<?php namespace Dnetix\Files;

use Exception;

/**
 * Class Settings
 * Stores anything in files.
 *
 * @author Diego Calle
 * @package Dnetix\Files
 */
class Settings {

    protected $folderPath;
    protected $name;

    protected $data;

    function __construct($name, $config = []) {
        foreach($config as $key => $value){
            $this->$key = $value;
        }
        if(!isset($config['folderPath'])){
            throw new Exception('A folderPath to store the settings must be declared');
        }
        $this->name = $name;
        $this->loadSettings();
    }

    public static function load($name, $config = []) {
        return new self($name, $config);
    }

    public function folderPath() {
        return $this->folderPath;
    }

    public function name() {
        return $this->name;
    }

    public function keyFile() {
        return $this->trailingSlash($this->folderPath(), $this->name());
    }

    public function trailingSlash($directory, $name = null) {
        if(substr($directory, -1) != '/') {
            $directory = $directory.'/';
        }
        if(!is_null($name)){
            return $directory.$name;
        }
        return $directory;
    }

    public function get($item) {
        if(isset($this->data[$item])){
            return $this->data[$item];
        }
        return null;
    }

    public function set($item, $value = null) {
        if(is_object($item) || is_array($item)){
            $this->data = $item;
        }else{
            $this->data[$item] = $value;
        }
        return $this;
    }

    private function loadSettings() {
        if(file_exists($this->keyFile())){
            $this->data = unserialize(file_get_contents($this->keyFile()));
        }else{
            if(!is_dir($this->folderPath())){
                throw new Exception("No existe el directorio para las configuraciones");
            }
            if(!is_writable($this->folderPath())){
                throw new Exception("El directorio de configuraciones no permite la escritura");
            }
        }
    }

    public function store() {
        $this->data = file_put_contents($this->keyFile(), serialize($this->data));
    }

    function __call($name, $arguments){
        if(is_object($this->data)){
            return $this->data->{$name}($arguments);
        }else{
            if(isset($this->data[$name])){
                return $this->data[$name];
            }
        }
        return null;
    }

    function __destruct(){
        $this->store();
    }

}
