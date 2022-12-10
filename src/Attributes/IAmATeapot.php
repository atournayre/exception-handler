<?php

namespace Atournayre\Component\ExceptionHandler\Attributes;

use Symfony\Component\HttpFoundation\Response;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IAmATeapot implements StatusCodeProvider
{
    public function getStatusCode(): int
    {
        return Response::HTTP_I_AM_A_TEAPOT;
    }
}
