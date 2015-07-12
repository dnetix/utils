<?php  namespace Dnetix\Hierachical;

/**
 * Class CategoriesTree
 * Implements the HierarchicalTree to print it as HTML ordered list or select in this case the nodes need
 * to have a name method
 *
 * @author Diego Calle
 * @package Dnetix\Hierachical
 */
class CategoriesTree extends HierarchicalTree {

    public $urlTo = '';
    public $prefixIdElement = 'node';
    public $classElement = 'node';

    public static function load($collection){
        $categoriesTree = new static;
        foreach($collection as $category){
            $categoriesTree->addNode($category->id(), $category, $category->parentId());
        }
        return $categoriesTree;
    }

    public function asOrderedList(array $options = []){
        $toReturn[] = '<ul'.$this->parseOptions($options).'>';
        $currentLevel = null;

        foreach($this as $node){
            if(is_null($currentLevel)){
                $currentLevel = $node->level();
            }else if($node->level() > $currentLevel){
                $toReturn[] = "<ul>";
                $currentLevel++;
            }else if($node->level() < $currentLevel){
                while($node->level() < $currentLevel){
                    $toReturn[] = "</ul>";
                    $currentLevel--;
                }
            }

            $toReturn[] = '<li id="'.$this->prefixIdElement.'_'.$node->key().'" class="'.$this->classElement.'" data-id="'.$node->key().'">'.$node->data()->name().'</li>';
        }

        while($currentLevel > 0){
            $toReturn[] = "</ul>";
            $currentLevel--;
        }

        $toReturn[] = "</ul>";

        return implode("\n", $toReturn);
    }

    public function asHTMLSelect($name, $selectedId, $options = [], $blankOption = null){
        $select[] = '<select name="'.$name.'"'.$this->parseOptions($options).'>';
        if(!is_null($blankOption)){
            $select[] = "<option value=\"\">{$blankOption}</option>";
        }
        foreach($this as $node){
            $selectedTag = ($node->key() == $selectedId) ? ' selected="selected"' : '';
            $optionContent = is_object($node->data()) ? $node->data()->name() : $node->data();
            $select[] = "<option value=\"{$node->key()}\"{$selectedTag}>{$optionContent}</option>";
        }
        $select[] = "</select>";
        return implode("\n", $select);
    }

    private function parseOptions($options){
        if(is_array($options)) {
            $parsedOptions = [];
            foreach ($options as $tag => $value) {
                $parsedOptions[] = $tag . '="' . $value . '"';
            }
            if (sizeof($parsedOptions) > 0) {
                return ' ' . implode(' ', $parsedOptions);
            }
        }
        return '';
    }

}