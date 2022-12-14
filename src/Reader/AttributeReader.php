<?php

namespace Atournayre\Component\ExceptionHandler\Reader;

use Atournayre\Component\ExceptionHandler\Contracts\AttributeReaderInterface;
use Atournayre\Component\ExceptionHandler\Contracts\StatusCodeProvider;

class AttributeReader implements AttributeReaderInterface
{
    private function getSupported(object $object, string $fqdn): array
    {
        $reflectionClass = new \ReflectionClass($object);
        $attributes = $reflectionClass->getAttributes();

        $instances = array_map(
            fn(\ReflectionAttribute $attribute) => $attribute->newInstance(),
            $attributes
        );

        return array_filter(
            $instances,
            fn(object $attribute) => $attribute instanceof $fqdn
        );
    }

    public function has(object $object, string $fqdn): bool
    {
        $supported = $this->getSupported($object, $fqdn);
        return $supported !== [];
    }

    public function get(object $object, string $fqdn): mixed
    {
        $supported = $this->getSupported($object, $fqdn);

        if ($supported == []) {
            return null;
        }

        return $supported[array_key_first($supported)];
    }
}
