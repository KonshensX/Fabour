<?php

namespace AppBundle\Controller;

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

        // Get the form
        $form = $this->createForm(MessageType::class, null);

        // Handle the form request
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
        }
        $response->setData($request->request);
        return $response;
    }
}
