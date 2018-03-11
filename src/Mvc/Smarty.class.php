<?php

namespace Ascmvc\Mvc;


class Smarty {
    
    /**@var Object:\Smarty|null  Contains a Smarty instance.*/
    protected static $smartyInstance;
    
    /**
     * Protected method : this class cannot be instantiated by the new keyword
     * because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    protected function __construct()
    {
        
    }
    
    /**
     * Protected method : this class cannot be copied because it is a Singleton.
     *
     * @param void.
     *
     * @return void.
     */
    protected function __clone()
    {
    
    }
    
    /**
     * Static method : returns the Singleton instance of this class.
     *
     * @param void.
     *
     * @return Object:\Smarty  Returns the current \Smarty object.
     */
    public static function getInstance()
    {
        if(!self::$smartyInstance) {
        
            self::$smartyInstance = new \Smarty();
        
        }
        
        return self::$smartyInstance;
    }
    
}