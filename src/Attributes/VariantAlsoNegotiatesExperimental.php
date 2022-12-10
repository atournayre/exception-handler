<?php

namespace Atournayre\Component\ExceptionHandler\Attributes;

use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS)]
class VariantAlsoNegotiatesExperimental implements StatusCodeProvider
{
    public function getStatusCode(): int
    {
        return Response::HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL;
    }
}