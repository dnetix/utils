<?php  namespace Dnetix\Files;

/**
 * Class FileWrapper
 * An utility class to use with Symfony\Component\HttpFoundation\File\UploadedFile
 * for handling basic stuff
 *
 * @author Diego Calle
 * @package Dnetix\Files
 */
class FileWrapper {

    private $file;

    protected $upload_path;
    protected $filename;
    protected $override = false;
    protected $base_path;

    function __construct($file) {
        $this->file = $file;
    }

    public static function create($file){
        return new static($file);
    }

    public function setUploadPath($uploadPath){
        $this->base_path = $uploadPath;
        $this->upload_path = $uploadPath;
        return $this;
    }

    public function setFilename($filename){
        $this->filename = $filename . $this->extension();
        return $this;
    }

    public function setOverride($bool){
        $this->override = $bool;
        return $this;
    }

    public function uploadPath(){
        return $this->upload_path;
    }

    public function basePath(){
        return $this->base_path;
    }

    public function fileName(){
        return $this->filename;
    }

    public function fullPathWithName($relative = true){
        if($relative){
            return $this->basePath().$this->filename();
        }else{
            return $this->uploadPath().$this->filename();
        }
    }

    public function moveIt(){
        $this->checkFilename();
        return $this->file->move($this->uploadPath(), $this->filename());
    }

    public function checkFilename(){
        if(is_null($this->filename)){
            $this->filename = $this->file->getClientOriginalName();
        }
        if(!$this->override){
            while(file_exists($this->fullPathWithName(false))){
                $this->filename = $this->getJustFilename().'_1'.$this->extension();
            }
        }
        $this->filename = str_replace(' ', '', $this->filename);
    }

    public function getFile(){
        return $this->file;
    }

    public function getJustFilename(){
        $parts = explode('.', $this->filename);
        array_pop($parts);
        return implode('.', $parts);
    }

    public function extension(){
        return '.'.$this->file->getClientOriginalExtension();
    }

    public function getClientSize(){
        return $this->file->getClientSize();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function original() {
        return $this->file;
    }

    public function isImage(){
        if(substr($this->file->getMimeType(), 0, 5) == 'image') {
            return true;
        }else{
            return false;
        }
    }

}