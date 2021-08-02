<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class movimiento3FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder


          ->add('receptor', TextType::class, ['label' => 'Responsable de entrega:','data'=>''])
          ->add('areaEntrega', EntityType::class, ['label' => 'Área Destino:','placeholder' => 'Seleccione', 'class' => 'AppBundle\Entity\departamento'])
          ->add('areaDestino', EntityType::class, ['label' => 'Departamento Destino:','placeholder' => 'Seleccione', 'class' => 'AppBundle\Entity\departamento'])
          ->add('autorizado', TextType::class, ['label' => 'Autorizado:','data'=>'Bárbara S. López'])
          ->add('respAFT', TextType::class, ['label' => 'Responsable AFT:','data'=>''])
          ->add('aprobado', TextType::class, ['label' => 'Aprobado:','data'=>'Dr. Lázaro Pérez']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'AppBundle\Entity\movimientoI',
        'csrf_protection' => false,
        'csrf_field_name' => '_token',
        // a unique key to help generate the secret token

      ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundlemovimiento3form_type';
    }
}
