<?php

namespace Atournayre\Component\ExceptionHandler\Contracts;

interface AttributeReaderInterface
{
    public function has(object $object, string $fqdn): bool;

    public function get(object $object, string $fqdn): mixed;
}
