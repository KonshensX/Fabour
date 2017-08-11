<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $em = $this->getDoctrine()->getEntityManager();
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
                // Do i really need to have a field for the title ?? // OFC it's the subject
                $message->setTitle($form['title']->getData());

                $message->setReceiver($form['receiver']->getData());
                $message->setTime(new \DateTime("now"));

                $em->persist($message);
                $em->flush();

                $response->setData([
                    'data' => 'Message was sent'
                ]);
            }
        }
        return $response;
    }

    /**
     * @Route("/message", name="message")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function messageAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder(array())
            ->add('title', TextType::class, array(
                'label' =>  'Subject'
            ))
            ->add('receiver', TextType::class, array(
                'label' =>  'Receiver'
            ))
            ->add('content', TextType::class,array(
                'label' =>  'Message content'
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Send message'
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {

            $message = new Message();
            $user = $this->getUser()->getUsername();

            $message->setTitle($form['title']->getData());
            $message->setSender($user);
            $message->setReceiver($form['receiver']->getData());
            $message->setTime(new \DateTime());
            $message->setContent($form['content']->getData());

            $em->persist($message);
            $em->flush();

            $this->addFlash('message-success', 'Message was successfully sent');

            $url = $this->generateUrl('message');
            return $this->redirect($url);

        }

        return $this->render('AppBundle:Message:new.html.twig', array(
            'form'  =>  $form->createView()
        ));

    }

    /**
     * @Route("/full/{id}", name="full_message")
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fullMessageAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        // This is the message that is going to be displayed
        $message = $em->getRepository('AppBundle:Message')->findOneBy(array('id'    =>  $id));
        // The sender of the email
        $sender = $em->getRepository('AppBundle:User')->findOneBy(['username' => $message->getSender()]);
        return $this->render('AppBundle:Message:full.html.twig', array(
            'message' => $message,
            'sender' => $sender,
        ));
    }
}
