<?php

namespace AppBundle\Form;

use AppBundle\Entity\componente;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class componentesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modelo',TextType::class, array(
                'required' => false,
            ))
            ->add('serie',TextType::class, array(
                'required' => true,
            ))
            ->add('marca',TextType::class)
            ->add('watts',TextType::class)
            ->add('sata', ChoiceType::class, array(
                'choices' => array('Si' => 'si', 'No' => 'no'),
                'placeholder' => 'Seleccione',
                'label' => 'Sata',
                'attr' => array('style' => 'width: 150px;')))
            ->add('capacidad',TextType::class)
            ->add('velocidad',TextType::class)
            ->add('lga',TextType::class)
            ->add('ram',TextType::class)
            ->add('optico', ChoiceType::class, array(
                'label' => 'Optico',
                'placeholder' => 'Seleccione',
                'choices' => array('Si' => 'si', 'No' => 'no'), 'attr' => array('style' => 'width: 200px;')))
            ->add('conector', TextType::class, array(
                'label' => 'Tipo de conector'
            ))

            ->add('estacion2', EntityType::class, array(
                'class' => 'AppBundle\Entity\inventario',
                'label' => false,
                'required' => false,
                'attr' => array('hidden' => 'true'),
                'query_builder' => function (EntityRepository $i) {
                    return $i->findInventarios();
                }
            ))
            ->add('tipo', TextType::class, array(
                'label' => 'Tipo',
                'attr' => array('style' => 'width: 150px;'),
                ))
//            ->add('ranuraVideo', TextType::class, array(
//                'label' => 'Ranura de Video',
//                'attr' => array('style' => 'width: 150px;'),
         //   ))
            ->add('tipoComponente', ChoiceType::class, array(
                'label' => 'Tipo de Componente',
                'placeholder' => 'Seleccione',
                //'placeholder'=>'Seleccione un componente',
                'choices' => array('Bocina' => 'bocina', 'Fuente' => 'fuente', 'Hdd' => 'hdd', 'Lector' => 'lector', 'Microprocesador' => 'microprocesador', 'Motherboard' => 'motherboard',
                    'Mouse' => 'mouse', 'Teclado' => 'teclado', 'Tarjeta de video' => 'tarjeta_video', 'Ram' => 'ram'), 'attr' => array('style' => 'width: 200px;')));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\componente',
            'csrf_protection' => false,
            'csrf_field_name' => '_token',
            'validation_groups' => true
            // a unique key to help generate the secret token

        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundlecomponentes_form_type';
    }
}
