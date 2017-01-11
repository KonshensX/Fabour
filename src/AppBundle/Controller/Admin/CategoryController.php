<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
* @Route("/category")
*/
class CategoryController extends Controller
{
    /**
    * @Route("/", name="catindex")
    */
    public function addCatAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        //Getting all the categories in the database
        $categories = $em->getRepository('AppBundle:Category')->findAll();

        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('submit', SubmitType::class)
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = new Category();
            $category->setCat($form['name']->getData());

            $em->persist($category);
            $em->flush();
        }

        return $this->render('AppBundle:Admin\Category:add_cat.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
    * @Route("/deleteCategory/{id}", name="deleteCategory")
    */
    public function deleteCatAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AppBundle:Category')->findOneBy(['id' => $id]);

        $em->remove($category);
        $em->flush();
        return $this->redirect($this->generateUrl('catindex'));
    }
}
