<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/profile")
     */
    public function profileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $user = $user->getUsername();

        $repo = $em->getRepository('AppBundle:PersonalInfo')->FindOneBy(array('username'    =>  $user));

        $items = $em->getRepository('AppBundle:Post')->findBy(array('owner'  =>  $user));

        $messages = $em->getRepository('AppBundle:Message')->findBy(array('receiver'  =>  $user));
        /**
         * Update the user informations
         */
        $form = $this->container->get('form.factory')->createNamedBuilder('userinfo-form', FormType::class)
                ->add('fname', TextType::class, array(
                    'label' => 'First name',
                    'data' => $repo->getFirstname()
                ))

                ->add('lname', TextType::class, array(
                    'label' => 'Last name',
                    'data' => $repo->getLastname()
                ))
                ->add('phone', TextType::class, array(
                    'label' => 'Phone number',
                    'data' => $repo->getMobile()
                ))
                ->add('interests', TextType::class, array(
                    'label' => 'Interests',
                    'data' => $repo->getInterests()
                ))
                ->add('occupation', TextType::class, array(
                    'label' => 'Occupation',
                    'data' => $repo->getOccupation()
                ))
                ->add('about', TextType::class, array(
                    'label' => 'About',
                    'data' => $repo->getAbout()
                ))
                ->add('submit', SubmitType::class, array(
                    'label' => 'Save Changes'
                ))
            ->getForm();

        $form->handleRequest($request);


        /**
         * Upload the avatar for the user profile
         */
        $avatar = $this->container->get('form.factory')->createNamedBuilder('avatar-form', FormType::class)
            ->add('avatar', FileType::class, array(
                'label' => 'Please upload image '
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Save Changes'
            ))
            ->getForm();

        $avatar->handleRequest($request);

        if($request->isXmlHttpRequest()) {
            if($request->getMethod() === "POST") {

                die();
                if($request->request->has('avatar-form')) {

                    if($avatar->isValid() && $avatar->isSubmitted()) {
                        /**
                         * @var Symfony\Component\HttpFoundation\File\UploadedFile $file
                         */
                        $file = $avatar['avatar']->getData();
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();
                        $file->move(
                            $this->getParameter('profile_directory'),
                            $fileName
                        );
                        $repo->setImage($fileName);



                        //$em->flush();

                        $response = new JsonResponse();
                        $response->setData(array(
                            'title' => "Image successfully updated",
                            'message'   =>  "The image was successfully updated"
                        ));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }
                /*
                if($request->request->has('userinfo-form')){

                    if($form->isValid() && $form->isSubmitted()) {
                        //this is supposed to update the personal informations on the profile page
                        $repo->setFirstname($form['fname']->getData());
                        $repo->setLastname($form['lname']->getData());
                        $repo->setMobile($form['phone']->getData());
                        $repo->setInterests($form['interests']->getData());
                        $repo->setOccupation($form['occupation']->getData());
                        $repo->setAbout($form['about']->getData());

                        $em->flush();

                        $response = new JsonResponse();
                        $response->setData(array(
                            'title'   =>  "Profile informations were updated successfully!",
                            'message'   =>  "Profile informations were updated successfully and now is a good time to go get some food"
                        ));
                        $response->headers->set('Content-type', 'application/json');
                        return $response;
                    }
                }
                */
            }
        }

        var_dump($request->request);
        return $this->render('AppBundle:Profile:profile.html.twig', array(
            'info'  =>  $repo,
            'form'  =>  $form->createView(),
            'avatar' => $avatar->createView(),
            'items'  =>  $items,
            'messages' => $messages
        ));
    }

    /**
     * @Route("/update", name="profile_update")
     */
    public function updateProfileAction() {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->getUser();

        $user = $user->getUsername();

        $entity = $em->getRepository('AppBundle:PersonalInfo')->findOneBy(array('username'  =>  $user));

        return var_dump($entity);
    }

}
