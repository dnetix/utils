<?php
namespace Dnetix\Files;

/**
 * Class FileCopier
 * Gets a file from a place, like another url and copies to another
 */
class FileCopier {

    private $origin;
    private $destPath;
    private $fileName;
    private $extension;

    private $contents;
    private $error;

    private $overwrite = false;

    /**
     * FileCopier constructor.
     * @param $origin
     * @param $destPath
     */
    public function __construct($origin, $destPath) {
        $this->origin = $origin;
        $this->destPath = $destPath;
    }

    private function validate(){
        // Check if the destination its a directory and its writable
        $destination = realpath($this->destPath);
        if(!is_dir($destination)){
            throw new \Exception("The destination provided its not a directory");
        }
        if(!is_writable($destination)){
            throw new \Exception("The destination provided its not writable");
        }
    }

    private function getContents() {
        if (!$this->contents) {
            $this->validate();

            $this->contents = file_get_contents($this->origin);

            // Obtains the mime type for the contents
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->buffer($this->contents);

            // With the mimeType try to get the extension
            $extensions = MimeTypes::extensionsOfMimeType($mimeType);
            if(!$extensions){
                throw new \Exception("No se pudo encontrar una extension para el archivo de origen: " . $mimeType);
            }
            $this->extension = $extensions[0];

            if(!$this->overwrite){
                while(file_exists($this->realDestination())){
                    $this->fileName = $this->fileName . '_1';
                }
            }
        }
        return $this->contents;
    }

    public function fileName(){
        if(!$this->fileName){
            preg_match_all('/\/([^\/]+?)(?:$|\?|\.)/', $this->origin, $matches);
            $name = end($matches[1]);
            $this->fileName = $name;
        }
        return $this->fileName;
    }

    public function extension(){
        return $this->extension;
    }

    public function realDestination(){
        if(!$this->contents){
            return null;
        }
        return $this->destPath . '/' . $this->fileName() . $this->extension();
    }

    public function realName() {
        if(!$this->contents){
            return null;
        }
        return $this->fileName() . $this->extension();
    }

    public function copyIt() {
        try {
            $this->getContents();
            if(!$this->fileName()){
                throw new \Exception("A name couldnt be found, please set one");
            }
            file_put_contents($this->realDestination(), $this->getContents());
            return true;
        }catch(\Exception $e){
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function setFileName($name) {
        $this->fileName = $name;
        return $this;
    }

    public function setOverwrite($overwrite) {
        $this->overwrite = $overwrite;
        return $this;
    }

    public function error() {
        return $this->error;
    }

    public static function create($origin, $destPath) {
        return new self($origin, $destPath);
    }

}