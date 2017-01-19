<?php

namespace AppBundle\Controller;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchController extends Controller
{
    public function searchBarRenderAction(Request $request) {
        $form = $this->createFormBuilder(null)
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

        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){
            $data = array(
                'term' => $form['item']->getData(),
                'category' => $form['category']->getData(),
                'city' => $form['city']->getData()
            );

            return $this->redirectToRoute('app_post_search', array(
                'term' => $data['term'],
                'category' => $data['category']->getCat(),
                'city' => $data['city']->getCity()
            ));

        }
        
        return $this->render('AppBundle:Search:searchbar.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
