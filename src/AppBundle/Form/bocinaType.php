<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class bocinaType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('marca')
      ->add('serie')
      ->add('modelo')
    ;
  }

  public function configureOptions(OptionsResolver $resolver)
  {

    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\bocinas',
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundlebocina_type';
  }
}
