<?php

namespace Atournayre\Component\ExceptionHandler\Handler;

use Atournayre\Component\ExceptionHandler\Contracts\AttributeReaderInterface;
use Atournayre\Component\ExceptionHandler\Contracts\StatusCodeProvider;
use Atournayre\Component\ExceptionHandler\Reader\AttributeReader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

class HttpStatusCodeExceptionHandler implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly RouterInterface $router,
        private readonly AttributeReaderInterface $attributeReader = new AttributeReader,
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

        if (!$this->hasAttribute($exception)) {
            return;
        }

        $this->setResponse($event);
    }

    private function hasAttribute(Throwable $exception): bool
    {
        return $this->attributeReader->has($exception, StatusCodeProvider::class);
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
        $attribute = $this->attributeReader->get($exception, StatusCodeProvider::class);

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

        try {
            $this->requestStack->getSession()
                ->getFlashBag()
                ->add('error', $exception->getMessage());
        } catch (SessionNotFoundException $exception) {}

        $route = $event->getRequest()->get('_route');
        $routeParameters = $event->getRequest()->get('_route_params');
        $url = $this->router->generate($route, $routeParameters);

        $response = new RedirectResponse($url);
        $event->setResponse($response);
    }
}
