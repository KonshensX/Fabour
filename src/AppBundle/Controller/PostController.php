<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Images;
use AppBundle\Entity\Post;
use AppBundle\Entity\Category;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class PostController
 * @package AppBundle\Controller
 * @Route("/post")
 */
class PostController extends Controller
{

    /**
    * @Route("/search/{term}/{category}/{city}")
    **/
    public function searchAction(Request $request, $term, $category, $city) {

      $em = $this->getDoctrine()->getEntityManager();

      $repo = $em->getRepository('AppBundle:Post')->findBy(array('title' => $term, 'price' => '999'));

      return $this->render('AppBundle:Post:search.html.twig', array(
          'result' => $repo
      ));

    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //Getting the current user in FOSuserBundle

        $post = new Post();

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, array(
                'required'  =>  false
            ))
            ->add('description', TextType::class, array(
                'required'  =>  false
            ))
            ->add('price', MoneyType::class, array(
                'required'  =>  false
            ))
            ->add('category', EntityType::class, array(
                // query choices from this entity
                'class' => 'AppBundle:Category',

                // use the User.username property as the visible option string
                'choice_label' => 'cat',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ))
            ->add('city', EntityType::class, array(
                // query choices from this entity
                'class' => 'AppBundle:City',

                // use the User.username property as the visible option string
                'choice_label' => 'city',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ))
            ->add('phone', TextType::class, array(
                'required'  =>  false
            ))
            ->add('email', TextType::class, array(
                'required'  =>  false
            ))
            ->add('image', FileType::class, array(
                'required'  =>  false
            ))
            ->add('add', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {
            $user = $this->getUser()->getUsername();
            $file = $post->getImage();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );

            $post->setImage($fileName);
            $post->setOwner($user);
            $post->setDate(new \DateTime());

            $em->persist($post);
            $em->flush();

            $this->addFlash('post-add', 'Post added successfully');
            $url = $this->generateUrl('upload_test', array('id' => $post->getId()));
            return $this->redirect($url);
        }

        return $this->render('AppBundle:Post:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

                /**
                 * @Route("/edit/{id}")
                 */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $repo = $em->getRepository('AppBundle:Post')->findOneBy(array('id'  =>  $id));

        $form = $this->createFormBuilder($repo)
                ->add('title', TextType::class)
                ->add('description', TextType::class)
                ->add('price', MoneyType::class)
                ->add('category', EntityType::class, array(
                    // query choices from this entity
                    'class' => 'AppBundle:Category',

                    // use the User.username property as the visible option string
                    'choice_label' => 'cat',

                    // used to render a select box, check boxes or radios
                    // 'multiple' => true,
                    // 'expanded' => true,
                ))
                ->add('city', EntityType::class, array(
                    // query choices from this entity
                    'class' => 'AppBundle:City',

                    // use the User.username property as the visible option string
                    'choice_label' => 'city',

                    // used to render a select box, check boxes or radios
                    // 'multiple' => true,
                    // 'expanded' => true,
                ))
                ->add('phone', TextType::class)
                ->add('email', TextType::class)
                ->add('image', FileType::class, array(
                    "label" =>  "Upload an image please",
                    'data_class' => null
                ))
                ->add('add', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()) {
            /**
             * @var Symfony\Component\HttpFoundation\File\UploadedFile $file
             */
            $file = $repo->getImage();

            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('images_directory'),
                $fileName
            );

            $repo->setImage($fileName);

            $em->persist($repo);
            $em->flush();

            $this->addFlash('notice', 'Post updated successfully');

            $url = $this->generateUrl('app_post_edit', array('id' => $id));
            return $this->redirect($url);

        }

        return $this->render('AppBundle:Post:edit.html.twig', array(
            'form'  =>  $form->createView()
        ));
    }

    /**
     * @Route("/delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Post:delete.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/full/{id}")
     */
    public function fullAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('AppBundle:Post')->findOneBy(array('id'  =>  $id));
        $images = $em->getRepository('AppBundle:Images')->findBy(array('post_id' =>  $id));

        return $this->render('AppBundle:Post:full.html.twig', array(
            'item'  =>  $repo,
            'images'    =>  $images
        ));
    }

    /**
     * @Route("/addView/{id}", name="add_view")
     */
    public function updateViewAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();

        $em->getRepository('AppBundle:Post')->incViews($em, $id);

        $response = new JsonResponse();
        $response->setData(array('message'    =>  'What the fuck i think it worked not really sure, if i can see this message from the browser it mleans it\'s working'));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

}
