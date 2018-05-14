<?php

namespace ACS;

/**
 * Class Aircraft
 * @package ACS
 */
class Aircraft
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $size;


    /**
     * Aircraft constructor.
     * @param $type string
     * @param $size string
     */
    public function __construct($type, $size)
    {
        $this->type = $type;
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

}
