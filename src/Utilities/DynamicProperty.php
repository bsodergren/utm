<?php

namespace UTM\Utilities;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use ReflectionProperty;

trait DynamicProperty
{
    private array $DynamicData = [];

    public function __set($name, $value): void
    {
        $prefix                            = Strings::after(__CLASS__, '\\', -1);
        $this->DynamicData[$prefix][$name] = $value;
    }

    public function __get(string $name)
    {
        $prefix = Strings::after(__CLASS__, '\\', -1);

        return $this->DynamicData[$prefix][$name] ?? null;
    }
}
