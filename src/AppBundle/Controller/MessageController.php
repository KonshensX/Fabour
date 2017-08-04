<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends Controller
{
    /**
     * @Route("/send", name="sendMessage")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendAction(Request $request) {
        $response = new JsonResponse();

        // I need to bind the form the message instance
        $message = new Message();
        // Get the form
        $form = $this->createForm(MessageType::class, null);

        // Handle the form request
        $form->handleRequest($request);
        // This means this is an ajax request
        if ($request->isXmlHttpRequest()) {
            // The form is all good
            if ($form->isValid() && $form->isSubmitted()) {
                // Filling the form manually i fucked up
                $message->setContent($form['message']->getData());
                $message->setSender($form['name']->getData());
                // The reciever is the owner of the post
//                $message->setReceiver()
                // Do i really need to have a field for the title ??
                $message->setTitle($form['title']->getData());
                // I have no idea why do i need this userId for 
//                $message->set//
            }
        }
        return $response;
    }
}
