<?php

/*
 * This file is part of the SoapBundle package.
 *
 * Original work (c) 2017 .NFQ | Netzfrequenz GmbH <info@nfq.de>
 * Modified work (c) 2018 Andrew Mikhailyk
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Barm\Bundle\SoapBundle;

use Barm\Bundle\SoapBundle\Event\RequestFinishedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * TraceableSoapClient.
 */
class TraceableSoapClient extends \SoapClient {
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var int
     */
    private $lastDuration = 0;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param string                   $wsdl
     * @param array                    $options
     */
    public function __construct(EventDispatcherInterface $dispatcher, $wsdl, $options = []) {
        if (isset($options['trace']) && !$options['trace']) {
            @trigger_error('TraceableSoapClient reset trace option to true', E_USER_NOTICE);
        }

        $options['trace'] = true;

        parent::__construct($wsdl, $options);
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        $startTime = microtime(true);
        $response = parent::__doRequest($request, $location, $action, $version, $one_way);
        $this->lastDuration = (microtime(true) - $startTime) * 1000;

        $event = new RequestFinishedEvent(
            $this->__getLastRequestHeaders(),
            $this->__getLastRequest(),
            $this->__getLastResponseHeaders(),
            $response,
            $this->lastDuration
        );
        $this->dispatcher->dispatch(BarmSoapEvents::REQUEST_FINISHED, $event);

        return $response;
    }

    /**
     * Get last request/responce cycle duration in milliseconds.
     *
     * @return int
     */
    public function __getLastDuration() {
        return $this->lastDuration;
    }
}
