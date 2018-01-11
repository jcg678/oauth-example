<?php
namespace AppBundle\Event;
use Symfony\Component\EventDispatcher\Event;

class PruebasEvent extends Event
{
    private $code;

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function out(){
        return "Out event";
    }
}