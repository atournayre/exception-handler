<?php

namespace Atournayre\Component\ExceptionHandler\Attributes\Http;

use Atournayre\Component\ExceptionHandler\Contracts\StatusCodeProvider;
use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS)]
class NotAcceptable implements StatusCodeProvider
{
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_ACCEPTABLE;
    }
}