<?php

namespace model\Sistema;

abstract class Model implements \JsonSerializable
{

	public function __set($attr, $val)
	{
		if(property_exists($this, $attr))
		{
			$this->$attr = $val;
		}
	}

	public function __get($attr)
	{
		return property_exists($this, $attr) ? $this->$attr : null;
	}

	public function jsonSerialize()
	{
		$obj = new \stdClass();
        $blackList = [];
        foreach(get_object_vars($this) as $attr => $value)
        {
            if(!in_array($attr, $blackList))
            {
                $obj->$attr = $value;
            }
        }
		return $obj;
	}
}