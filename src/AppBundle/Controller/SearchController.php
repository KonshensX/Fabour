<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    public function searchBarRenderAction(Request $request) {
        $form = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('searchHandler'))
            ->add('item', TextType::class)
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
            ))
            ->add('submit', SubmitType::class, array(
                'label' =>  'Search'
            ))
            ->getForm();
        
        return $this->render('AppBundle:Search:searchbar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/search", name="searchHandler")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function searchHandler(Request $request) {
        // dump($request->request);
        $em = $this->getDoctrine()->getEntityManager();
        $data = $request->request->get('form');
        dump($data['item']);

        // Get the data from the model
        $posts = $em->getRepository('AppBundle:Post')->findBy([
            'title' => $data['item'],
            'category_id' => $data['category'],
            'city_id' => $data['city']
        ]);

        // Send the data to the view
        return $this->render('AppBundle:Post:search.html.twig', [
            'result' => $posts
        ]);
    }
}
