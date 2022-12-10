<?php

namespace Atournayre\Component\ExceptionHandler\Attributes;

use Atournayre\Component\ExceptionHandler\Contracts\StatusCodeProvider;
use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS)]
class VersionNotSupported implements StatusCodeProvider
{
    public function getStatusCode(): int
    {
        return Response::HTTP_VERSION_NOT_SUPPORTED;
    }
}
