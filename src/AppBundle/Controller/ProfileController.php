<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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

        $form = $this->createFormBuilder(array())
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

        if($form->isValid() && $form->isSubmitted()) {

            //this is supposed to update the personal informations on the profile page
            $repo->setFirstname($form['fname']->getData());
            $repo->setLastname($form['lname']->getData());
            $repo->setMobile($form['phone']->getData());
            $repo->setInterests($form['interests']->getData());
            $repo->setOccupation($form['occupation']->getData());
            $repo->setAbout($form['about']->getData());

            $em->flush();
            $this->addFlash('success', 'Profile informations has been updated');

            $url = $this->generateUrl('app_profile_profile');
            return $this->redirect($url);
        }

        $avatar = $this->createFormBuilder(array())
            ->add('avatar', FileType::class, array(
                'label' => 'Please upload image '
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Save Changes'
            ))
            ->getForm();

        $avatar->handleRequest($request);

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

            $this->addFlash('notice', 'Profile picture successfully uploaded');

            $url = $this->generateUrl('app_profile_profile');
            return $this->redirect($url);
        }

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
