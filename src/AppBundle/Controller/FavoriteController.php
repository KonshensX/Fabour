<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FavoriteController extends Controller
{
    /**
     * @Route("/favoriteList", name="favoritesList")
     */
    public function favoriteListAction()
    {
        // I need to check if the user is logged in before giving permission
//        if (!$this->get('security.authorization_checker')->isGranted('AUTHENTICATED_FULLY')) {
//            return $this->redirectToRoute('homepage');
//        }
        // temp solutiton to check if the user is logged in
        if (!$this->getUser() instanceof User) {
            return false;
        }
        $em = $this->getDoctrine()->getManager();
        // The user ID
        $userID = $this->getUser()->getId();
        // Get all the posts favored by this user
        $ids = $em->getRepository('AppBundle:FavoritePost')->findBy(['userId' => $userID ]);
        // dump($ids);
        $posts = array();

        for($i = 0; $i < count($ids); $i++){
            $post = $em->getRepository('AppBundle:Post')->findOneBy(['id'   => $ids[$i]->getPostId() ]);
            array_push($posts, $post);
        }
        // dump($posts);
        // What the fuck this does
        // array_shift($posts);
        return $this->render('AppBundle:Favorite:favorite_list.html.twig', array(
            'posts' => $posts
        ));
    }

}
