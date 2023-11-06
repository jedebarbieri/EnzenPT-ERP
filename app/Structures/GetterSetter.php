<?php

namespace App\Structures;

trait GetterSetter
{
    public function __get($name)
    {
        $method = "get" . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        throw new \Exception(self::class . ": Property $name does not exist");
    }

    public function __set($name, $value)
    {
        $method = "set" . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }
        throw new \Exception(self::class . ": Property $name does not exist");
    }
}
