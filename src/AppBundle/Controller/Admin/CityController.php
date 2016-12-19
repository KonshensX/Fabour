<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
* @Route("/city")
*/
class CityController extends Controller
{
    /**
     * @Route("/")
     */
    public function addCityAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //getting all cities in database
        $cities = $em->getRepository('AppBundle:City')->findAll();

        $form = $this->createFormBuilder(null)
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('submit', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $city = new City();
            $city->setCity($form['name']->getData());

            $em->persist($city);
            $em->flush();
        }
        return $this->render('AppBundle:Admin\City:add_city.html.twig', array(
            'form' => $form->createView(),
            'cities' => $cities
        ));
    }

    /**
    * @Route("/delete/{id}", name="deleteCity")
    */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $city = $em->getRepository('AppBundle:City')->findOneBy(['id' => $id]);

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');
        $response->setData(['title' => 'Deleted', 'message' => 'It was deleted hooorah!!!!']);
        return $response;
    }

}
