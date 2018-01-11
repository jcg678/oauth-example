<?php
namespace AppBundle\EventSubscriber;

class PruebasEventSubscriber
{
    public function onCustomEvent($event)
    {
        dump($event->getCode());
    }

    public function eventoOut($event){
        dump($event->out());
    }

    public function onSuperEvento($event)
    {

        dump("LLAMANDO A LA FUNCIONALIDAD DE UN EVENTO");
    }
}