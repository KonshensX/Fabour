<?php

namespace AppBundle\Controller;

use AppBundle\Entity\PersonalInfo;
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

        // This is getting the information for the current user
        // This could be deleted, not really useful
        // I can change this to app.user.personalInfo in the twig
        // $repo = $em->getRepository('AppBundle:PersonalInfo')->FindOneBy(array('username'    =>  $user->getUsername()));

        // Get the posts of the user
        // This could be accessed using the app.user.posts
        // $items = $em->getRepository('AppBundle:Post')->findBy(array('owner'  =>  $user->getUsername()));

        // Get the messages of the user
        $messages = $em->getRepository('AppBundle:Message')->findBy(array('receiver'  =>  $user->getUsername()));

        /**
         * Update the user informations
         */
        // I have no idea what this check is used for
        if(!$user->getPersonalInfo()) {
            $repo = new PersonalInfo();
        }
        $form = $this->container->get('form.factory')->createNamedBuilder('userinfo-form', FormType::class)
                ->add('fname', TextType::class, array(
                    'label' => 'First name',
                    'data' => $user->getPersonalInfo()->getFirstname(),
                    'required' => false
                ))

                ->add('lname', TextType::class, array(
                    'label' => 'Last name',
                    'data' => $user->getPersonalInfo()->getLastname(),
                    'required' => false
                ))
                ->add('phone', TextType::class, array(
                    'label' => 'Phone number',
                    'data' => $user->getPersonalInfo()->getMobile(),
                    'required' => false
                ))
                ->add('interests', TextType::class, array(
                    'label' => 'Interests',
                    'data' => $user->getPersonalInfo()->getInterests(),
                    'required' => false
                ))
                ->add('occupation', TextType::class, array(
                    'label' => 'Occupation',
                    'data' => $user->getPersonalInfo()->getOccupation(),
                    'required' => false
                ))
                ->add('about', TextType::class, array(
                    'label' => 'About',
                    'data' => $user->getPersonalInfo()->getAbout(),
                    'required' => false
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
                        
                        $em->flush();

                        $response = new JsonResponse();
                        $response->setData(array(
                            'title' => "Image successfully updated",
                            'message'   =>  "The image was successfully updated"
                        ));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                }

                if($request->request->has('userinfo-form')){

                    if($form->isValid() && $form->isSubmitted()) {
                        //this is supposed to update the personal informations on the profile page
                        $repo->setUsername($this->getUser());
                        $repo->setFirstname($form['fname']->getData());
                        $repo->setLastname($form['lname']->getData());
                        $repo->setMobile($form['phone']->getData());
                        $repo->setInterests($form['interests']->getData());
                        $repo->setOccupation($form['occupation']->getData());
                        $repo->setAbout($form['about']->getData());

                        $em->persist($repo);
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

            }
        }

        return $this->render('AppBundle:Profile:profile.html.twig', array(
            // 'info'  =>  $repo,
            'form'  =>  $form->createView(),
            'avatar' => $avatar->createView(),
            // 'items'  =>  $items,
            'messages' => $messages
        ));
    }


    /**
     * This is the controller for the items fetching and rendering
     */
    public function itemsFetchingAction() {
        $em = $this->getDoctrine()->getManager();

        $items = $em->getRepository('AppBundle:Post')->findAll();

        return $this->render('AppBundle:Profile:items.html.twig',[
            'items' => $items
        ]);
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
