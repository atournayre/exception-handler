<?php

namespace Atournayre\Component\ExceptionHandler\Attributes;

use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS)]
class LengthRequired implements StatusCodeProvider
{
    public function getStatusCode(): int
    {
        return Response::HTTP_LENGTH_REQUIRED;
    }
}
