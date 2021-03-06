<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class impresoraFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('marca')
      ->add('modelo')
      ->add('numInventario')
      ->add('serie')
      ->add('tipo')
      ->add('tipoTonerCartucho')
      ->add('tonerCartucho');
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\impresora',
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundleimpresora_form_type';
  }
}
