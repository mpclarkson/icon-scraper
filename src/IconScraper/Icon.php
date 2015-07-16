<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 15/07/15
 * Time: 4:20 PM
 */

namespace Mpclarkson\IconScraper;

class Icon
{
    const APPLE_TOUCH = 'apple-touch';
    const FAVICON = 'favicon';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $href;

    /**
     * @var string
     */
    private $size;

    public function __construct($type, $href, $size)
    {
        $this->type = $type;
        $this->href = $href;
        $this->size = $size;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getHref()
    {
        return $this->href;
    }

    public function getSize()
    {
        return $this->size;
    }

}