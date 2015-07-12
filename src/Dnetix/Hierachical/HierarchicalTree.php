<?php  namespace Dnetix\Hierachical;

use Exception;

/**
 * Class HierarchicalTree
 * A PHP implementation for Linked List
 *
 * @author Diego Calle
 * @package Dnetix\Hierachical
 */
class HierarchicalTree implements \Iterator {

    /**
     * @var HierarchicalNode
     */
    public $root;
    /**
     * @var HierarchicalNode
     */
    public $index;
    /**
     * @var HierarchicalNode
     */
    public $lastNode;

    protected $stack = [];
    public $keys = [];

    public $limitedTo = null;

    public function __construct(){
        $this->root = new HierarchicalNode(0, 'root');
        $this->index = $this->root;
        $this->lastNode = $this->root;
    }

    /**
     * Adds a new node to the list, data can be anything a unique key has to be provided, and if no parentKey provided
     * it will be a root node
     * @param $key
     * @param $data
     * @param $parentKey
     * @throws Exception
     */
    public function addNode($key, $data, $parentKey = null){
        $this->reset();
        $parentKeys = [];

        if(empty($key)){
            throw new Exception("You must define a key for the node");
        }

        if(!is_null($parentKey)){
            $this->lastNode = $this->index();
            $node = $this->nextNode();
            while(!is_null($node) && $node->key() != $parentKey){
                $this->lastNode = $node;
                $node = $this->nextNode();
            }

            if(is_null($node)){
                throw new Exception("ParentKey: {$parentKey} not found", 1);
            }

            $lastStack = null;
            if($this->countStack() > 0){
                while($firstStack = $this->shiftStack()){
                    $lastStack = $firstStack;
                    $parentKeys[] = $firstStack->data()->key();
                }
            }

            if(is_null($lastStack) || $lastStack->data() != $node){
                $listNode = (new HierarchicalNode(
                    null, $node
                ))->setNext($node->next());
                $this->lastNode->setNext($listNode);
                $parentKeys[] = $parentKey;
            }else{
                $node = $this->getLastSameLevelNode($node);
            }

        }else{
            $node = $this->getLastSameLevelNode();
        }

        $newNode = (new HierarchicalNode(
            $key, $data, $parentKeys
        ));
        $node->setNext($newNode);

        $this->keys[] = $key;

        $this->reset();
    }

    /**
     * Iterates to the next node in the list, returns null when ended
     * @return HierarchicalNode
     */
    public function nextNode(){
        $node = $this->index();

        if(is_null($node->next())){
            while(is_null($node->next()) && $this->countStack() > 0){
                $node = $this->popStack();
                $this->lastNode = $node;
            }
            $node = $node->next();
        }else{
            $node = $node->next();
        }

        if(!is_null($node) && $node->isList()){
            $this->pushStack($node);
            $node = $node->data();
        }

        $this->index = $node;
        return $node;
    }

    /**
     * Returns the current node pointed
     * @return HierarchicalNode
     */
    public function index(){
        return $this->index;
    }

    protected function popStack(){
        return array_pop($this->stack);
    }

    protected function shiftStack(){
        return array_shift($this->stack);
    }

    protected function pushStack($node){
        array_push($this->stack, $node);
    }

    protected function countStack(){
        return count($this->stack);
    }

    /**
     * Obtains the last node from the same level, to add as last one.
     * @param null $lastNode
     * @return HierarchicalNode
     */
    public function getLastSameLevelNode($lastNode = null){
        if(is_null($lastNode)){
            $lastNode = $this->root;
        }
        while (!is_null($lastNode->next())) {
            $lastNode = $lastNode->next();
        }
        return $lastNode;
    }

    /**
     * Resets the list to iterate it again from the beginning
     * @param bool $respectLimited
     */
    public function reset($respectLimited = false){
        if($respectLimited && !is_null($this->limitedTo)){

            $this->index = $this->limitedTo;
        }else{
            $this->index = $this->root;
        }
        $this->lastNode = null;
        $this->stack = [];
    }

    /**
     * Limits the list to iterate only from the node given, can be a node or a key
     * @param $node
     * @return $this
     */
    public function limitListTo($node){
        if(!($node instanceof HierarchicalNode)){
            $node = $this->findNodeByKey($node);
        }
        $this->reset();
        $this->limitedTo = $node;
        return $this;
    }

    /**
     * Erase the limit setted to the list
     * @return $this
     */
    public function noLimit(){
        $this->limitedTo = null;
        return $this;
    }

    /**
     * Finds and return the node that matches the key provided, null if not exists
     * @param $key
     * @return HierarchicalNode|null
     */
    public function findNodeByKey($key){
        $this->reset();
        if(!in_array($key, $this->keys)){
            return null;
        }
        while($node = $this->nextNode()){
            if($node->key() == $key){
                return $node;
            }
        }
    }

    /** Implementation to use it with foreach */

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current() {
        if($this->index() == $this->root){
            return $this->nextNode();
        }
        return $this->index();
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next() {
        $this->nextNode();
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key() {
        if($this->index() == $this->root){
            $node = $this->nextNode();
            if(is_null($node)){
                return null;
            }
            return $node->key();
        }else if(is_null($this->index())){
            return null;
        }
        return $this->index()->key();
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid() {
        return is_null($this->key()) ? false : true;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind() {
        $this->reset(true);
    }

}