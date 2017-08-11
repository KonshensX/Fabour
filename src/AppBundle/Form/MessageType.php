<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Your name'
            ))
            ->add('receiver', HiddenType::class)
            ->add('phone', TextType::class, array(
                'label' => 'Your phone number'
            ))
            ->add('title', TextType::class, array(
                'label' => 'Subject'
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Your email'
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'Message'
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Send'
            ))
         ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'attr' => [
                'id' => 'contactForm',
            ],
        ]);
    }

}