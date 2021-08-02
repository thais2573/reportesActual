<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class inventarioFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $builder->add('componente', 'collection', array('type' => new perifericoType()));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\inventario',
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundleinventario_form_type';
  }
}
