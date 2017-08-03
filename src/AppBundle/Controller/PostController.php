<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FavoritePost;
use AppBundle\Entity\Images;
use AppBundle\Entity\Post;
use AppBundle\Entity\Category;
use AppBundle\Form\MessageType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

      $repo = $em->getRepository('AppBundle:Post')->search($em, $term);

      return $this->render('AppBundle:Post:search.html.twig', array(
          'result' => $repo
      ));

    }

    /**
     * @Route("/new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fullAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $isFavorated = null;
        $repo = $em->getRepository('AppBundle:Post')->findOneBy(array('id'  =>  $id));
        $images = $em->getRepository('AppBundle:Images')->findBy(array('post_id' =>  $id));
        if ($this->getUser()) {
            $isFavorated = $em->getRepository('AppBundle:FavoritePost')->findOneBy(['postId' => $id,
                'userId' => $this->getUser()->getId()]);
        }

        $messageForm = $this->createForm(MessageType::class, null, [
            'action' => $this->generateUrl('sendMessage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'method' => 'POST',
        ]);
        /*
        $messageForm->handleRequest($request);

        if($messageForm->isValid() && $messageForm->isSubmitted()) {
            $name = $messageForm['name']->getData();
            $phone = $messageForm['name']->getData();
            $email = $messageForm['name']->getData();
            $messageContent = $messageForm['name']->getData();

            $messageContent = "Name: " . $name . "<br>" .
                                "Phone: " . $phone . "<br>" .
                                $messageContent;
            // Send an email to the receiver
            $message = \Swift_Message::newInstance()
                ->setSubject($name . "Sent you a message concerning " .$repo->getTitle())
                ->setFrom($email)
                ->setTo($repo->getEmail())
                ->setBody($messageContent);

            $this->get('mailer')->send($message);
        }
        */

        return $this->render('AppBundle:Post:full.html.twig', array(
            'item'  =>  $repo,
            'isFaved' => $isFavorated,
            'images'    =>  $images,
            'message'   =>  $messageForm->createView()
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

    /**
     * @Route("/favorated/{id}", name="favorated")
     */
    public function favedAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $userID = $this->getUser()->getId();
        $exists = $em->getRepository('AppBundle:FavoritePost')->findOneBy([
            'userId'    =>  $userID,
            'postId'   =>  $id
        ]);
        $response->headers->set('Content-Type', 'application/json');
        if(!$exists) {
            if($id) {
                $favedPost = new FavoritePost();

                $favedPost->setPostId($id);
                $favedPost->setSavedAt(new \DateTime());
                $favedPost->setUserId($userID);

                $em->persist($favedPost);
                $em->flush();

                $response->setData([
                    'title' =>  'favorated'
                ]);
                $response->setStatusCode(200);
                return $response;
            }
        } else if($exists) {
            $em->remove($exists);
            $em->flush();
            $response->setData([
                'title' =>  'deleted'
            ]);
            return $response;
        }
    }

    /**
     * @Route("/checkFav/{id}", name="checkFav")
     */
    public function checkFavAction(Request $request, $id){
        $response = new JsonResponse();
        $em = $this->getDoctrine()->getManager();
        $userID = $this->getUser()->getId();
        $exists = $em->getRepository('AppBundle:FavoritePost')->findOneBy([
            'userId'    =>  $userID,
            'postId'   =>  $id
        ]);

        if(!$exists) {
            //if it doesn't exist add it
            $response->setData([
               'title' => "favorite"
            ]);
        } else if($exists) {
            $response->setData([
                'title' => "unfavorite"
            ]);
        }
        return $response;
    }

    /**
     * Favorite or UnFavorite a post
     *
     * @Route("/toggleStatus/{id}", name="toggleStatus")
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
     public function toggleStatusAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $response = new JsonResponse();
        $data = $em->getRepository('AppBundle:Post')->findOneBy(array('id' => $id));
         if ($data) {
             if ($data->getIsActive()) {
                 $data->setIsActive(false);
                 $em->persist($data);
                 $em->flush();
                 $response->setData(['message' => 'deactivated']);
             }  else {
                 $response->setData(['message' => 'activated']);
                 $data->setIsActive(true);
                 $em->persist($data);
                 $em->flush();
             }
         } else {
             throw new Exception("Oops, it appears that this item does not exist!");
         }
        return $response;
     }

    /**
     * @Route("/checkStatus/{id}", name="check_item_status")
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     * @internal param Request $request
     */
    public function checkStatusAction($id) {
        $response = new JsonResponse();
        $em =$this->getDoctrine()->getManager();

        $status = $em->getRepository('AppBundle:Post')->findOneBy(['id' => $id]);
        if (!$status) {
            throw new Exception("This item does not exist, please enter another one!");
            return;
        }
        $response->setData(['status' => $status->getIsActive()]);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Popular items for the bottom of the page
     * @Route("/testing", name="testingRecommendedItems")
     */
    public function popularItemsBottomAction() {
        $em = $this->getDoctrine()->getManager();
        /**
         * Views does not refer to drake album lel :'(
         */
//        $views = $em->getRepository('AppBundle:Post')->getViews($em);
//        $items = $em->getRepository('AppBundle:Post')->findBy([
//            'isActive' => true,
//            'views' => 5
//        ]);

        return $this->render('AppBundle:Post:popular.html.twig', [
        ]);
    }

}
