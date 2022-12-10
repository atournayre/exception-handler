<?php

namespace Atournayre\Component\ExceptionHandler\Attributes;

use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS)]
class RequestHeaderFieldsTooLarge implements StatusCodeProvider
{
    public function getStatusCode(): int
    {
        return Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE;
    }
}
