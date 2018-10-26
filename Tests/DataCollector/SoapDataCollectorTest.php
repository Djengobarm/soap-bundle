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

namespace Barm\Bundle\SoapBundle\Tests\DataCollector;

use PHPUnit\Framework\TestCase;
use Barm\Bundle\SoapBundle\DataCollector\SoapDataCollector;
use Barm\Bundle\SoapBundle\Event\RequestFinishedEvent;

/**
 * @see SoapDataCollector
 */
final class SoapDataCollectorTest extends TestCase {
    /**
     * @dataProvider dataProvideOnRequestFinished
     */
    public function testOnRequestFinished($events, $expectedTotal, $expectedTime) {
        $collector = new SoapDataCollector();

        foreach ($events as $event) {
            $collector->onRequestFinished($event);
        }

        $this->assertEquals($expectedTotal, $collector->getTotal());
        $this->assertEquals($expectedTime, $collector->getTime());
        $this->assertEquals($expectedTotal, count($collector->getRequests()));
    }

    public function dataProvideOnRequestFinished() {
        return array(
            array(array(), 0, 0),
            array(
                array(
                    new RequestFinishedEvent(
                        'Request headers',
                        'Request body',
                        'Respone headers',
                        'Response body',
                        12
                    ),
                ),
                1,
                12,
            ),
            array(
                array(
                    new RequestFinishedEvent(
                        'Request headers',
                        'Request body',
                        'Respone headers',
                        'Response body',
                        12
                    ),
                    new RequestFinishedEvent(
                        'Request headers',
                        'Request body',
                        'Respone headers',
                        'Response body',
                        28.8
                    ),
                ),
                2,
                40.8,
            ),
        );
    }
}
