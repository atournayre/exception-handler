<?php

namespace Atournayre\Component\ExceptionHandler\Handler;

use Atournayre\Component\ExceptionHandler\Contracts\StatusCodeProvider;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

class ExceptionHandler implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'handleException',
        ];
    }

    public function handleException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$this->supportedException($exception)) {
            return;
        }

        $this->setResponse($event);
    }

    private function supportedException(Throwable $exception): bool
    {
        return $this->getAttribute($exception) !== null;
    }

    private function getAttribute(Throwable $exception): ?StatusCodeProvider
    {
        $reflectionClass = new ReflectionClass($exception);
        $attributes = $reflectionClass->getAttributes();

        $instances = array_map(
            fn(ReflectionAttribute $attribute) => $attribute->newInstance(),
            $attributes
        );

        $supported = array_filter(
            $instances,
            fn(object $attribute) => $attribute instanceof StatusCodeProvider
        );

        if (count($supported) === 0) {
            return null;
        }

        return $supported[array_key_first($supported)];
    }

    private function setResponse(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $attribute = $this->getAttribute($exception);

        $response = new JsonResponse(
            [
                "error" => $exception->getMessage(),
            ],
            $attribute->getStatusCode()
        );

        $event->setResponse($response);
    }
}