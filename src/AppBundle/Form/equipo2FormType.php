<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class equipo2FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numInventario', TextType::class)
            ->add('modelo', TextType::class)
            ->add('serie', TextType::class)
            ->add('marca', TextType::class)
            ->add('sello', TextType::class)
            ->add('capacidad', TextType::class)
            ->add('color', TextType::class)
            ->add('tipo', TextType::class)
            ->add('tipoTonerCartucho', TextType::class)
            ->add('tonerCartucho', TextType::class)
            ->add('lcd', TextType::class)
            ->add('tipoEquipo', ChoiceType::class, [
                'choices' => [
                    'cpuchasis' => 'cpuchasis',
                    'monitor' => 'monitor',
                    'backup' => 'backup',
                    'scanner' => 'scanner',
                    'estabilizador' => 'estabilizador',
                    'impresora' => 'impresora',
                    'laptop' => 'laptop'
                ]])
//            ->add('componente', CollectionType::class, array(
//                'label' => 'Componentes',
//                'entry_type' => componentesFormType::class,
//                'allow_delete' => true,
//                'allow_add' => true,
//                'prototype' => true,
//                'delete_empty'=>true,
//                'by_reference' => false,
//                'attr' => array('class' => 'mi-componente', 'style' => 'display:none')
//
//            ))
            ->add('departamento', EntityType::class, array(
                'class' => 'AppBundle\Entity\departamento',
                'label' => 'Centro de Costo',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->findDepartamentos();
                }
            ))
            ->add('estacion', EntityType::class, array(
                'class' => 'AppBundle\Entity\inventario',
                'label' => 'Estacion',
                'required' => true,
                'query_builder' => function (EntityRepository $i) {
                    return $i->findInventarios();
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\equipo',
            'csrf_protection' => false,
            'csrf_field_name' => '_token',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundleequipo2form_type';
    }
}
