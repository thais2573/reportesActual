<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 2/15/2019
 * Time: 3:12 PM
 */

namespace AppBundle\Form;

use AppBundle\Entity\componente;
use AppBundle\Entity\cpuchasis;
use AppBundle\Entity\equipo;
use AppBundle\Entity\inventario;
use AppBundle\Entity\periferico;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Tests\Fixtures\ChoiceSubType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
class EstacionForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
   $builder
     ->add('responsable', TextType::class,array(
         'label' => 'Responsable :',
         'required'=>'true',
         'data'=>'',
     ))
     ->add('passSetup', TextType::class,array(
         'required'=>true
     ))
     ->add('ip', TextType::class,array(
         'required'=>true
     ))
    ->add('nombreRed', TextType::class,array(
        'required'=>true
    ))
       ->add('antivirus', TextType::class,array(
           'required'=>true
       ))
       ->add('passSetup', TextType::class,array(
           'required'=>true
       ))
     ->add('equipos', CollectionType::class, array(
       'entry_type' => equipoFomType::class,
       'by_reference' => false,
       'allow_delete' => true,
       'allow_add' => true,
       'attr' => array('class' =>  equipo::class, 'style' => 'display:none'),
       //'attr'         => array('class' => 'form-group row servicios_institucion', 'style' => 'display:none')
     ))
     ->add('centroCosto', EntityType::class, array(
       'class' => 'AppBundle\Entity\departamento',
       'label' => 'Centro de Costo',
       'required' => true,
       'query_builder' => function (EntityRepository $er) {
         return $er->findDepartamentos();
       }
     ))
      ->add('componente', CollectionType::class, array(
      'entry_type' => componentesFormType::class,
      'by_reference' => false,
      'allow_delete' => true,
      'allow_add' => true,
      'prototype'=>true,
      'attr' => array('class' => 'componente', 'style' => 'display:none')
  ));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\inventario',
      'csrf_protection' => false,
      'csrf_field_name' => '_token',
        'validation_groups' => false,
      // a unique key to help generate the secret token

    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundleinventario_form';
  }




}