<?php

namespace Atournayre\Component\ExceptionHandler\Handler;

use Atournayre\Component\ExceptionHandler\Contracts\StatusCodeProvider;
use ReflectionAttribute;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

class ExceptionHandler implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
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
        if ($this->isMainRequest($event)) {
            $this->setResponseForMainRequest($event);
            return;
        }

        $this->setResponseForJson($event);
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return bool
     */
    protected function isMainRequest(ExceptionEvent $event): bool
    {
        return $event->isMainRequest() == HttpKernelInterface::MAIN_REQUEST;
    }

    private function setResponseForMainRequest(ExceptionEvent $event)
    {
        if ($this->isJsonRequest($event)) {
            $this->setResponseForJson($event);
            return;
        }

        $this->setNominalResponse($event);
    }

    private function isJsonRequest(ExceptionEvent $event): bool
    {
        $contentType = $event->getRequest()->headers->get('Content-Type');
        return str_contains($contentType, 'json');
    }

    private function setResponseForJson(ExceptionEvent $event)
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

    private function setNominalResponse(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $this->requestStack->getSession()->getFlashBag()->add('error', $exception->getMessage());

        $route = $event->getRequest()->get('_route');
        $routeParameters = $event->getRequest()->get('_route_params');
        $url = $this->router->generate($route, $routeParameters);

        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }
}
