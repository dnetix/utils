<?php
namespace Dnetix\Files;

class CSVHelper {
    private $contents;
    private $delimiter;

    public function __construct($contents) {
        $this->contents = $contents;
        $this->lookDelimiter();
    }

    public function getDelimiter() {
        if (is_null($this->delimiter)) {
            $this->lookDelimiter();
        }
        return $this->delimiter;
    }

    private function lookDelimiter() {
        $options = [',', ';'];
        $count = 0;
        $actual = 0;
        foreach ($options as $i => $char) {
            $number = substr_count($this->contents, $char);
            if ($number > $count) {
                $count = $number;
                $actual = $i;
            }
        }
        $this->delimiter = $options[$actual];
    }

    public function cleanText() {
        $this->contents = preg_replace('/\s+' . $this->getDelimiter() . '/', $this->getDelimiter(), $this->contents);
        $this->contents = preg_replace('/' . $this->getDelimiter() . '\s+/', $this->getDelimiter(), $this->contents);
        return $this;
    }

    public function content() {
        return $this->contents;
    }

    public function toArray() {
        return array_map(function ($line) {
            if(strlen($line) > 1) {
                $line = trim($line);
                return explode($this->getDelimiter(), $line);
            }
        }, explode("\n", $this->content()));
    }

    public static function fromFile($fileroute){
        if(!file_exists($fileroute)){
            throw new \Exception("There is no file " . $fileroute, 1);
        }
        return new self(file_get_contents($fileroute));
    }
}