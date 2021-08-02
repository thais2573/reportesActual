<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class chasisFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('color')
      ->add('fechaMantenimiento')
      ->add('modelo')
      ->add('numInventario')
      ->add('sello');
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\equipo',
      'csrf_protection' => false,
      'csrf_field_name' => '_token',
      // a unique key to help generate the secret token

    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundlechasis_form_type';
  }
}
