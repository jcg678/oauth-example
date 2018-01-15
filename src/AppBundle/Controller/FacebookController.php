<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Event\PruebasEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;


class FacebookController extends Controller
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/facebook", name="connect_facebook")
     */
    public function connectAction()
    {
        // will redirect to Facebook!
        return $this->get('oauth2.registry')
            ->getClient('facebook_main')// key used in config.yml
            ->redirect();
    }

    /**
     * After going to Facebook, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config.yml
     *
     * @Route("/connect/facebook/check", name="connect_facebook_check")
     */
    public function connectCheckAction(Request $request)
    {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient $client */
        $client = $this->get('oauth2.registry')
            ->getClient('facebook_main');

        try {
            /** @var \League\OAuth2\Client\Provider\FacebookUser $user */
            $user = $client->fetchUser();
            // do something with all this new power!
            $user->getFirstName();
            // ...
        } catch (IdentityProviderException $e) {
            // something went wrong!
            var_dump($e->getMessage());
            die();
        }


        $id = $user->getId();
        $check = 0;
        //Check if user exist with googleID
        $userIN = $this->getDoctrine()->getManager()->getRepository('AppBundle:User')->findOneBy(['facebookID' => $id]);
        dump($userIN);
        if ($userIN != null) {
            $check = 1;
        }

        if ($check == 0) {
            //if user not exist create a new with googleID
            $userManager = $this->get('fos_user.user_manager');
            $userRes = $userManager->createUser();
            $userRes->setUsername($user->getEmail());
            $userRes->setEmail($user->getEmail());
            $userRes->setEnabled(1);
            $userRes->setEmailCanonical($user->getEmail());
            $userRes->setPlainPassword($user->getId());
            $userManager->updateUser($userRes);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $userlast = $em->getRepository('AppBundle:User')->find($userRes->getID());
            $userlast->setFacebookID($user->getId());
            $em->persist($userlast);
            $em->flush();

        }

        //login manually
        if ($check == 1) {
            $token = new UsernamePasswordToken($userIN, null, 'main', $userIN->getRoles());
        }else{
            $token = new UsernamePasswordToken($userlast, null, 'main', $userlast->getRoles());
        }

        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);



        return $this->render('facebook/infacebook.html.twig', [
            'user' => $user,
            'check' => $check
        ]);

    }
}