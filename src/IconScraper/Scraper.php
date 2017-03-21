<?php

namespace Mpclarkson\IconScraper;

class Scraper
{
    protected $url = '';
    protected $dataAccess;

    public function __construct($args = array())
    {
        if (isset($args['url'])) {
            $this->url = $args['url'];
        }

        $this->dataAccess = new DataAccess();
    }

    /**
     * @param string $url
     */
    public static function baseUrl($url, $path = false)
    {
        $return = '';

        if (!$url = parse_url($url)) {
            return false;
        }

        // Scheme
        $scheme = isset($url['scheme']) ? strtolower($url['scheme']) : null;
        if ($scheme != 'http' && $scheme != 'https') {
            return false;
        }
        $return .= "{$scheme}://";

        // Username and password
        if (isset($url['user'])) {
            $return .= $url['user'];
            if (isset($url['pass'])) {
                $return .= ":{$url['pass']}";
            }
            $return .= '@';
        }

        // Hostname
        if (!isset($url['host'])) {
            return false;
        }

        $return .= $url['host'];

        // Port
        if (isset($url['port'])) {
            $return .= ":{$url['port']}";
        }

        // Path
        if ($path && isset($url['path'])) {
            $return .= $url['path'];
        }
        $return .= '/';

        return $return;
    }

   public function info($url) {
        if (empty($url) || $url === false) {
            return false;
        }

        $headers = $this->dataAccess->retrieveHeader($url);

        // leaves only numeric keys
        $status_lines = array_filter($headers, function ($key) {
            return is_int($key);
        }, ARRAY_FILTER_USE_KEY);

        // uses last returned status line header
        $exploded = explode(' ', end($status_lines));

        if (! array_key_exists(1, $exploded)) {
            return false;
        }

        list(, $status) = $exploded;

        if (isset($headers['location'])) {
            $url = $headers['location'];
        }

        return ['status' => $status, 'url' => $url];
    }

    /**
     * @param false|string $url
     *
     * @return string
     */
    public function endRedirect($url) {
        $out = $this->info($url);
        return !empty($out['url']) ? $out['url'] : false;
    }

    /**
     * @return array of icons or empty array
     **/
    public function get($url = '')
    {

        // URLs passed to this method take precedence.
        if (!empty($url)) {
            $this->url = $url;
        }

        $url = rtrim($this->endRedirect($this->baseUrl($this->url, false)), '/');

        return $this->getIcons($url);
    }

    /**
     * @param string $url
     */
    private function getIcons($url) {

        if (empty($url)) {
            return [];
        }

        $html = $this->dataAccess->retrieveUrl("{$url}/");
        preg_match('!<head.*?>.*</head>!ims', $html, $match);

        if (empty($match) || count($match) == 0) {
            return [];
        }

        $head = $match[0];

        $icons = [];

        $dom = new \DOMDocument();

        // Use error supression, because the HTML might be too malformed.
        if (@$dom->loadHTML($head)) {
            $links = $dom->getElementsByTagName('link');

            foreach ($links as $link) {

                if ($link->hasAttribute('rel') && $href = $link->getAttribute('href')) {

                    $attribute = $link->getAttribute('rel');

                    // Make sure the href is an absolute URL.
                    if ($href && filter_var($href, FILTER_VALIDATE_URL) === false) {
                        $href = $url . '/' . $href; //Todo: Improve this
                    }

                    $size = $link->hasAttribute('sizes') ? $link->getAttribute('sizes') : [];
                    $size = !is_array($size) ? explode('x', $size) : $size;

                    $type = false;

                    switch(strtolower($attribute)) {
                        case Icon::APPLE_TOUCH:
                            $type = Icon::APPLE_TOUCH;
                            break;
                        default:
                            if(strpos($link->getAttribute('href'), 'icon') !== FALSE) {
                                $type = Icon::FAVICON;
                                $size = [];
                            }
                    };

                    if(!empty($type) && filter_var($href, FILTER_VALIDATE_URL)) {
                        $icons[] = new Icon($type, $href, $size);
                    }
                }
            }
        }

        //Sort the icons by width
        usort($icons, function($a, $b) {
            return $a->getWidth() - $b->getWidth();
        });

        //If it is empty, try and get one from the root
        if (empty($icons)) {
            $icons = $this->getFavicon($url);
        }

        return $icons;
    }

    private function getFavicon($url) {

        // Try /favicon.ico first.
        $info = $this->info("{$url}/favicon.ico");
        if ($info['status'] == '200') {
            $favicon = $info['url'];
        }

        // Make sure the favicon is an absolute URL.
        if (isset($favicon) && filter_var($favicon, FILTER_VALIDATE_URL) === false) {
            $favicon = $url . '/' . $favicon;
        }

        if (isset($favicon)) {
            return [
                new Icon(Icon::FAVICON, $favicon, [])
            ];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param DataAccess $dataAccess
     */
    public function setDataAccess($dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }
}
