<?php
/**
 * @file plugins/generic/optimetaCitations/classes/PID/Handle.php
 *
 * Copyright (c) 2021+ TIB Hannover
 * Copyright (c) 2021+ Gazi Yucel
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class Handle
 * @ingroup plugins_generic_optimetacitations
 *
 * @brief Handle class
 */

namespace APP\plugins\generic\optimetaCitations\classes\PID;

class Handle
{
    /**
     * Correct prefix
     * @var string
     */
    public string $prefix = 'https://hdl.handle.net';

    /**
     * Incorrect prefixes
     *
     * @var array|string[]
     */
    public array $prefixInCorrect = [
        'http://hdl.handle.net',
        'http://www.hdl.handle.net',
        'https://www.hdl.handle.net'];

    /**
     * Add prefix to PID
     *
     * @param string|null $pid
     * @return string
     */
    public function addPrefixToPid(?string $pid): string
    {
        if (empty($pid)) return '';

        return $this->prefix . '/' . trim($pid, ' /');
    }

    /**
     * Remove prefix from URL
     *
     * @param string|null $url
     * @return string
     */
    public function removePrefixFromUrl(?string $url): string
    {
        if (empty($url)) return '';

        return trim(str_replace($this->prefix, '', $url), ' ./');
    }

    /**
     * Normalize PID in a string by removing any (in)correct prefixes, e.g.
     * http://... > https:....
     *
     * @param string $string
     * @param string $pid 10.12345/tib.11.2.3, Q12345678, w1234567890
     * @return string
     */
    public function normalize(string $string, string $pid): string
    {
        if (empty($string) || empty($pid)) return '';

        $doiListToRemove = [];

        foreach ($this->prefixInCorrect as $key) {
            $doiListToRemove[] = $key . '/' . $pid;
        }

        return trim(str_replace($doiListToRemove, $this->addPrefixToPid($pid), $string));
    }

    /**
     * Extract PID from string, e.g. 10.12345/tib.11.2.3
     *
     * @param string $string
     * @return string
     */
    public function extractFromString(string $string): string
    {
        if (empty($string)) return '';

        $url = new Url();

        $match = $url->extractFromString($string);

        if(str_contains($match, $this->prefix)) {
            return $this->removePrefixFromUrl(trim($match, ' ./'));
        }

        return '';
    }
}
