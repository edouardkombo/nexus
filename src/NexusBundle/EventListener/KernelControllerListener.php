<?php

namespace NexusBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class KernelControllerListener
{
    private $logger;
    private $log = [];

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $this->log['Request Method'] = $event->getRequest()->getMethod();
        $this->log['Request Format'] = $event->getRequest()->getRequestFormat();
        $this->log['Base URL'] = $event->getRequest()->getBaseUrl();
        $this->log['URI Path Info'] = $event->getRequest()->server->get('PATH_INFO');
        $this->log['URI Query String'] = $event->getRequest()->getQueryString();
        $this->log['Full URI'] = $event->getRequest()->getUri();
        $this->log['Is Method Correct'] = $event->getRequest()->isMethod('GET') ? 'Correct method' : 'Incorrect method';
        $this->log['Is URI Age Param Set'] = $event->getRequest()->attributes->get('age') ? 'Yes' : 'No';
        $this->log['Controller'] = $event->getController();
        $this->log['Router'] = $event->getRequest()->attributes->get('_route');
        $this->log['Client IP'] = $event->getRequest()->getClientIp();
        $this->log['Router Parameters'] = json_encode($event->getRequest()->attributes->get('_route_params'));

        $this->logger->info(json_encode($this->log));
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $this->log['Response'] = $event->getResponse()->getContent();

        $this->logger->info(json_encode($this->log));
    }
}