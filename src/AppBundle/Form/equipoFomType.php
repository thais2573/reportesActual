<?php

namespace AppBundle\Form;


use AppBundle\Entity\componente;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class equipoFomType extends AbstractType
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
      ->add('tipoEquipo',ChoiceType::class, [
        'choices'  => [
          'cpuchasis' => 'cpuchasis',
          'monitor' => 'monitor',
          'backup' => 'backup',
          'scanner'=>'scanner',
          'estabilizador'=>'estabilizador',
          'impresora'=>'impresora',
            'laptop'=>'laptop'
        ]])
//      ->add('componente', CollectionType::class, array(
//        'entry_type' => componentesFormType::class,
//        'by_reference' => false,
//        'allow_delete' => true,
//        'allow_add' => true,
//        'prototype'=>true,
//        'attr'         => array('class' => 'componente1', 'style' => 'display:none')
//      ))

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
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\equipo',
      'csrf_protection' => false,
      'csrf_field_name' => '_token',
        'validation_groups' => false
      // a unique key to help generate the secret token

    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundleequipo_fom_type';
  }
}
