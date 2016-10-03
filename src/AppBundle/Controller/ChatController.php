<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ChatController extends Controller
{
    /**
     * @Route("/chat")
     */
    public function chatAction()
    {

        return $this->render('AppBundle:Chat:chat.html.twig', array(

        ));
    }

}
