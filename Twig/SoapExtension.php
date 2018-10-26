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

namespace Barm\Bundle\SoapBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SoapExtension extends AbstractExtension {

    /**
     * {@inheritdoc}
     */
    public function getFilters() {
        return array(
            new TwigFilter('barm_soap_pretty_xml', array($this, 'formatXml')),
        );
    }

    /**
     * Pretty format an xml string.
     *
     * @param string $xml
     * @param int    $indentSize
     * @param string $indentCharacter
     *
     * @return string
     */
    public function formatXml($xml) {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        $dom->formatOutput = true;
        return $this->clearOutput($dom->saveXML());
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return get_class($this);
    }

    private function clearOutput($str) {
        return str_replace('<?xml version="1.0"?>', '', $str);
    }
}
