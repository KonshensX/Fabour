<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Entity\Images;
use AppBundle\Entity\PersonalInfo;
use AppBundle\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /*==========================================*/
    /* This is the renderer of the navbar along with the profile picture */
    /*==========================================*/
    public function profilePictureAction() {

        /*if (!$this->getuser()) {
            return new Response("This is a guest user!");
        }*/
        $em = $this->getDoctrine()->getManager();
        if ($this->getUser())
        {
            $repo = $em->getRepository('AppBundle:PersonalInfo')->findOneBy(['username' => $this->getUser()->getUsername()]);
        } else {
            $repo = new PersonalInfo();
        }

        if ($repo->getImage()) {
            $profilePicture =  $repo->getImage();
        } else {
            $profilePicture = 'profilePicture.png';
        }


        return $this->render('AppBundle::navbar.html.twig', [
           'profilepic' => $profilePicture
        ]);
    }

    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('AppBundle:Post')->findActivePosts();

        /*Form handling submission*/

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig' , array(
            'items' => $repo,
        ));
    }

    /**
     * @Route("/message", name="message")
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
     */
    public function fullMessageAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('AppBundle:Message')->findOneBy(array('id'    =>  $id));

        return $this->render('AppBundle:Message:full.html.twig', array(
            'message' => $message
        ));
    }

    /**
     * @Route("/upload/{id}", name="upload_test")
     */
    public function uploadAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('AppBundle:Post')->findOneBy(array('id'  =>  $id));
        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            //$query = $em->createQuery('SELECT COUNT(p.id) FROM AppBundle:Post p');
            //$result = $query->getResult();
            //do some shit like get pic and stuff

            foreach($request->files as $uploadedFile) {
                $image = new Images();
                $fileName = md5(uniqid()).'.'.$uploadedFile->guessExtension();
                $uploadedFile->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
                $image->setName($fileName);
                $image->setPostId($id);
                $em->persist($image);
                $em->flush();
                $em->clear();
            }
        }
        return $this->render('AppBundle:Profile:upload.html.twig', array(
            'form'  =>  $form->createView(),
            'id'    =>  $id
        ));
    }


}
