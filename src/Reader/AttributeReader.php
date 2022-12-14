<?php

namespace Atournayre\Component\ExceptionHandler\Reader;

use Atournayre\Component\ExceptionHandler\Contracts\AttributeReaderInterface;

class AttributeReader implements AttributeReaderInterface
{
    public function has(object $object, string $fqdn): bool
    {
        $reflectionClass = new \ReflectionClass($object);
        $attributes = $reflectionClass->getAttributes();

        $instances = array_map(
            fn(\ReflectionAttribute $attribute) => $attribute->newInstance(),
            $attributes
        );

        $supported = array_filter(
            $instances,
            fn(object $attribute) => $attribute instanceof $fqdn
        );

        return $supported !== [];
    }
}
