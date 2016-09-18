<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Hardware;
use AppBundle\Form\HardwareType;

/**
 * Hardware controller.
 *
 * @Route("/hardware")
 */
class HardwareController extends Controller
{
    /**
     * Lists all Hardware entities.
     *
     * @Route("/", name="hardware_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $hardwares = $em->getRepository('AppBundle:Hardware')->findAll();

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $hardwares,
            $request->query->get('page', 1),
            10
        );

        return $this->render('hardware/index.html.twig', array(
            'pagination'    => $pagination
        ));
    }

    /**
     * Creates a new Hardware entity.
     *
     * @Route("/new", name="hardware_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $hardware = new Hardware();
        $form = $this->createForm('AppBundle\Form\HardwareType', $hardware);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($hardware);
            $em->flush();

            return $this->redirectToRoute('hardware_new');
        }

        return $this->render('hardware/new.html.twig', array(
            'hardware' => $hardware,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Hardware entity.
     *
     * @Route("/{id}", name="hardware_show")
     * @Method("GET")
     */
    public function showAction(Hardware $hardware)
    {
        $deleteForm = $this->createDeleteForm($hardware);

        return $this->render('hardware/show.html.twig', array(
            'hardware' => $hardware,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Hardware entity.
     *
     * @Route("/{id}/edit", name="hardware_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Hardware $hardware)
    {
        $deleteForm = $this->createDeleteForm($hardware);
        $editForm = $this->createForm('AppBundle\Form\HardwareType', $hardware);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($hardware);
            $em->flush();

            return $this->redirectToRoute('hardware_edit', array('id' => $hardware->getId()));
        }

        return $this->render('hardware/edit.html.twig', array(
            'hardware' => $hardware,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Hardware entity.
     *
     * @Route("/{id}", name="hardware_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Hardware $hardware)
    {
        $form = $this->createDeleteForm($hardware);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($hardware);
            $em->flush();
        }

        return $this->redirectToRoute('hardware_index');
    }

    /**
     * Creates a form to delete a Hardware entity.
     *
     * @param Hardware $hardware The Hardware entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Hardware $hardware)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('hardware_delete', array('id' => $hardware->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
