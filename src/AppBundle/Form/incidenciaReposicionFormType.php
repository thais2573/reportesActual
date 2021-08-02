<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class incidenciaReposicionFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('receptor', TextType::class, ['label' =>'Receptor:','data'=>'' ])
      ->add('respEntrega', TextType::class, ['label' => 'Responsable de Entrega:','data'=>' Elio Mendoza'])
      ->add('areaEntrega', TextType::class, ['label' => 'Departamento de Entrega','data'=>'Taller TECUN'])
      ->add('areaDestino', EntityType::class, ['label' => 'Área Destino:','placeholder' => 'Seleccione', 'class' => 'AppBundle\Entity\departamento'])
      ->add('autorizado', TextType::class, ['label' => 'Autorizado:','data'=>'Bárbara S. López'])
      ->add('tipoMovimiento', TextType::class, ['label'=>'Tipo de Movimiento:'])
      ->add('respAFT', TextType::class, [  'label' => 'Responsable AFT:'])
      ->add('aprobado', TextType::class, ['label' => 'Aprobado:','data'=>'Dr. Lázaro Pérez']);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\movimiento',
      'csrf_protection' => false,
      'csrf_field_name' => '_token',
      // a unique key to help generate the secret token

    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundleincidencia_reposicion_form_type';
  }
}
