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
        if( !isset($url['host']) ) {
            return false;
        }

        $return .= $url['host'];

        // Port
        if (isset($url['port'])) {
            $return .= ":{$url['port']}";
        }

        // Path
        if( $path && isset($url['path']) ) {
            $return .= $url['path'];
        }
        $return .= '/';

        return $return;
    }

    public function info($url)
    {
        if(empty($url) || $url === false) {
            return false;
        }

        $max_loop = 5;

        // Discover real status by following redirects. 
        $loop = true;
        while ($loop && $max_loop-- > 0) {
            $headers = $this->dataAccess->retrieveHeader($url);
            $exploded = explode(' ', $headers[0]);

            if( !isset($exploded[1]) ) {
                return false;
            }
            list(,$status) = $exploded;

            switch ($status) {
                case '301':
                case '302':
                    $url = $headers['Location'];
                    break;
                default:
                    $loop = FALSE;
                    break;
            }
        }

        return array('status' => $status, 'url' => $url);
    }

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

    private function getIcons($url) {

        if(empty($url)) {
            return false;
        }

        $html = $this->dataAccess->retrieveUrl("{$url}/");
        preg_match('!<head.*?>.*</head>!ims', $html, $match);

        if(empty($match) || count($match) == 0) {
            return false;
        }

        $head = $match[0];

        $icons = [];

        $dom = new \DOMDocument();
        // Use error supression, because the HTML might be too malformed.
        if (@$dom->loadHTML($head)) {
            $links = $dom->getElementsByTagName('link');

            foreach ($links as $link) {

                if($link->hasAttribute('rel') && $href = $link->getAttribute('href')) {

                    $attribute = $link->getAttribute('rel');

                    // Make sure the href is an absolute URL.
                    if($href && filter_var($href, FILTER_VALIDATE_URL) === false ) {
                        $href = $url . '/' . $href; //Todo: Improve this
                    }

                    $size = $link->hasAttribute('sizes') ? $link->getAttribute('sizes') : [];

                    switch(strtolower($attribute)) {
                        case Icon::APPLE_TOUCH:
                            $type = Icon::APPLE_TOUCH;
                            $size = explode('x', $size);
                            break;
                        default:
                            if(strpos($link->getAttribute('href'), 'icon') !== FALSE) {
                                $type = Icon::FAVICON;
                                $size = [];
                            }
                    };

                    if(isset($type)) {
                        $icons[] = new Icon($type, $href, $size);
                    }
                }
            }
        }

        //Sort the icons by width
        usort($icons, function($a, $b)  {
            return $a->getWidth() - $b->getWidth();
        });

        return $icons;
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
