<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FavoriteController extends Controller
{
    /**
     * @Route("/favoriteList")
     */
    public function favoriteListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userID = $this->getUser()->getId();
        $ids = $em->getRepository('AppBundle:FavoritePost')->findBy(['userId' => $userID ]);
        $posts = array();

        for($i = 0; $i < count($ids); $i++){
            $post = $em->getRepository('AppBundle:Post')->findOneBy(['id'   => $ids[$i]->getPostId() ]);
            array_push($posts, $post);
        }
        array_shift($posts);

        return $this->render('AppBundle:Favorite:favorite_list.html.twig', array(
            'posts' => $posts
        ));
    }

}
