<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Event\PruebasEvent;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {

        // replace this example code with whatever you need

        $authChecker = $this->container->get('security.authorization_checker');
        $router = $this->container->get('router');

        if ($authChecker->isGranted('ROLE_ADMIN') ) {
            return $this->render('otro/welcome.html.twig');
        }
        if ($authChecker->isGranted('ROLE_USER')  ) {

            return $this->render('otro/welcome.html.twig');
        }

        return $this->render('otro/main.html.twig');
    }


    public function eventoAction(Request $request)
    {
        // replace this example code with whatever you need
        // Creamos el objeto del evento
        $event = new PruebasEvent();
        //$event->setCode(200);
        // Lanzar evento
        $eventDispatcher = $this->get('event_dispatcher');
        $eventDispatcher->dispatch('custom.event.pruebas_event', $event);
        return $this->render('otro/evento.html.twig');



    }


}
