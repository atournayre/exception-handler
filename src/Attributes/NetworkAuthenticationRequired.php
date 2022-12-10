<?php

namespace Atournayre\Component\ExceptionHandler\Attributes;

use Atournayre\Component\ExceptionHandler\Contracts\StatusCodeProvider;
use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS)]
class NetworkAuthenticationRequired implements StatusCodeProvider
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED;
    }
}
