<?php  namespace Dnetix\Storage;

/**
 * Class FileSession
 * Stores information on files mented to use as a shared session in files.
 * The function that you can implement is
 *
function shared_session($key = null, $default = null){
    $user = currentUser();
    if(is_null($user)){
        throw new Exception("No hay un usuario activo para crear la session compartida");
    }
    $sharedSession = app('file_session')->setSharedId($user->id());
    if(is_null($key))
        return $sharedSession;
    if(is_array($key)){
        return $sharedSession->put(($key));
    }
    return $sharedSession->get($key, $default);
}
 *
 * @author Diego Calle
 * @package Dnetix\Storage
 */
class FileSession {

    public $shared_id;
    public $session_path;
    public $timestamp;

    function __construct($sessionPath, $sharedId = null) {
        $this->session_path = $sessionPath;
        $this->shared_id = is_null($sharedId) ? rand(0, 9999) : $sharedId;
        $this->timestamp = time();
    }

    public function sharedId(){
        return $this->shared_id;
    }

    public function setSharedId($sharedId){
        $this->shared_id = $sharedId;
        return $this;
    }

    public function sessionPath(){
        return $this->session_path;
    }

    public function timestamp(){
        return $this->timestamp;
    }

    /**
     * Stores the information on the file
     * @param $data
     * @param null $key
     * @return $this
     */
    public function put($data, $key = null) {
        if(is_array($data) && is_null($key)){
            foreach($data as $key => $value){
                $this->filePut($key, $value);
            }
        }else{
            $this->filePut($key, $data);
        }
        return $this;
    }

    /**
     * Obtains the information of a session file by key
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null) {
        if($this->fileExists($key)){
            $sessionData = $this->fileObtain($key);
            return $this->getSessionValue($sessionData);
        }
        return $default;
    }

    /**
     * Deletes a sesion file
     * @param $key
     * @return $this
     */
    public function forget($key) {
        if($this->fileExists($key)){
            $this->fileDelete($key);
        }
        return $this;
    }

    private function sessionFile($key){
        return $this->sessionPath().$this->sharedId().'_'.$key;
    }

    private function codeSessionValue($value){
        return serialize([
            'value' => $value,
            'meta' => [
                'timestamp' => $this->timestamp()
            ]
        ]);
    }

    private function decodeSessionData($serialized){
        return unserialize($serialized);
    }

    private function getSessionValue($unserialized){
        return $unserialized['value'];
    }

    private function fileExists($key){
        return file_exists($this->sessionFile($key));
    }

    private function fileObtain($key) {
        $contents = file_get_contents($this->sessionFile($key));
        return $this->decodeSessionData($contents);
    }

    private function filePut($key, $value) {
        file_put_contents($this->sessionFile($key), $this->codeSessionValue($value));
    }

    private function fileDelete($key) {
        unlink($this->sessionFile($key));
    }

}