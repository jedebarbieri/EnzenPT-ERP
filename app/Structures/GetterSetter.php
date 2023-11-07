<?php

namespace App\Structures;

use Illuminate\Support\Str;

/**
 * This trait is used to get and set private or protected properties. 
 * The getter or setter function should be in camelCase
 * with "get" as prefix for getters and "set" as prefix for setters
 * and should append "Attribute" as suffix.
 * 
 * Example:
 *      // Declaration
 *      public function getMyPropertyAttribute()
 * 
 *      // Access from outside
 *      $objetc->my_property;
 * 
 * Remember that "my_property" might not exist as a property in the class, this 
 * could only be an accessor.
 * As a convention, the related private property is always in camelCase
 * 
 * Example:
 * 
 *     // Private storage property declaration
 *     private $myProperty = null;
 * 
 * 
 * @property bool $strictMode If this is set to true, an exception will be thrown if the property does not exist.
 *                            Otherwise, the property will be returned as if it was public.
 */
trait GetterSetter
{

    private bool $strictMode = false;

    public function setStrictMode(bool $strictMode)
    {
        $this->strictMode = $strictMode;
    }

    /**
     * This method is used to get the value of a property when it is private or protected.
     * The $name parameter is the property in snake_case.
     * 
     * @param string $name This is the name of the property in snake_case, accessed from outside
     */
    public function __get($name)
    {
        /**
         * @var string $ccName This is the name of the related private property
         */
        $ccName = Str::camel($name);

        // This is the corresponding getter method name
        $method = "get" . Str::camel($ccName) . "Attribute";

        if (method_exists($this, $method)) {
            return $this->$method();
        }
        if (property_exists($this, $ccName)) {
            if ($this->strictMode) {
                throw new \Exception(self::class . ": Property $name not accessible");
            }
            return $this->$ccName;
        }
        throw new \Exception(self::class . ": Property $name does not exist");
    }

    /**
     * This method is used to set the value of a property when it is private or protected.
     * The $name parameter is the property in snake_case.
     * 
     * @param string $name This is the name of the property in snake_case, accessed from outside
     * @param $value This is the value to be set
     */
    public function __set($name, $value)
    {
        /**
         * @var string $ccName This is the name of the related private property
         */
        $ccName = Str::camel($name);
        
        // This is the corresponding getter method name
        $method = "set" . Str::camel($ccName) . "Attribute";

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        if (property_exists($this, $ccName)) {
            if ($this->strictMode) {
                throw new \Exception(self::class . ": Property $name not accessible");
            }
            return $this->$ccName = $value;
        }
        throw new \Exception(self::class . ": Property $name does not exist");
    }
}
