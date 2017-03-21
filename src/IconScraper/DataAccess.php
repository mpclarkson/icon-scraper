<?php

namespace Mpclarkson\IconScraper;

/**
 * DataAccess is a wrapper used to read/write data locally or remotely
 * Aside from SOLID principles, this wrapper is also useful to mock remote resources in unit tests
 * Note: remote access warning are silenced because we don't care if a website is unreachable
 **/
class DataAccess {

    public function retrieveUrl($url) {
        $this->setContext();
        return @file_get_contents($url);
    }
	
    public function retrieveHeader($url) {
        $this->setContext();

        // get_headers already follows redirects and stacks headers up in array
        $headers = @get_headers($url, TRUE);
        $headers = array_change_key_case($headers);

        // if there were multiple redirects flatten down the location header
        if (isset($headers['location']) && is_array($headers['location'])) {
            $headers['location'] = array_filter($headers['location'], function ($header) {
                return strpos($header, '://') !== false; // leave only absolute urls
            });

            $headers['location'] = end($headers['location']);
        }

        return $headers;
    }
    
	
    public function saveCache($file, $data) {
        file_put_contents($file, $data);
    }
    
    public function readCache($file) {
        return file_get_contents($file);
    }
    
    private function setContext() {
        stream_context_set_default(
            array(
                'http' => array(
                    'method' => 'GET',
                    'timeout' => 10,
                    'header' => "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:20.0; Favicon; +https://github.com/mpclarkson/icons-craper) Gecko/20100101 Firefox/32.0\r\n",
                )
            )
        );
    }
}