<?php  namespace Dnetix\Hierachical;

/**
 * Class HierarchicalNode
 * Node class for the Hierarchical Tree
 *
 * @author Diego Calle
 * @package Dnetix\Hierachical
 */
class HierarchicalNode {

    public $key;
    public $data;
    public $parentKeys = [];

    public $next;

    function __construct($key = null, $data = null, &$parentKeys = []) {
        $this->key = $key;
        $this->data =& $data;
        $this->parentKeys = $parentKeys;
    }

    public function key(){
        return $this->key;
    }

    public function data(){
        return $this->data;
    }

    public function &next(){
        return $this->next;
    }

    public function level(){
        return count($this->parentKeys());
    }

    public function parentKeys(){
        return $this->parentKeys;
    }

    public function isList(){
        return is_null($this->key()) ? true : false;
    }

    public function setKey($key){
        $this->key = $key;
        return $this;
    }

    public function setNext(&$next){
        $this->next = $next;
        return $this;
    }

}