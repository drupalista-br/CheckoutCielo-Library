<?php
class Commons{
    //warnings
    public $warnings = array();
    
    /**
     * Set Warnings
     * 
     * @param $message == (array) Key holds the field name and Value holds the Message to be set or unset
     * @param $action  == 1 sets, 0 unsets
     * 
     */    
    public function setWarning($message, $action = 1){
        if($action){
            $this->warnings[$message[0]] = $message[1];
        }else{
            unset($this->warnings[$message[0]]);
        }
    }     
}

?>