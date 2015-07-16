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
    const APPLE_TOUCH = 'apple-touch-icon';
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
     * @var array
     */
    private $size;

    public function __construct($type, $href, array $size)
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

    public function getWidth()
    {
        return array_key_exists(0, $this->size) ? $this->size[0] : null;
    }

    public function getHeight()
    {
        return array_key_exists(1, $this->size) ? $this->size[1] : null;
    }
}