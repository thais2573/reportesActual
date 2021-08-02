<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class productoFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('idProducto', TextType::class)
      ->add('descripcion', TextType::class)
      ->add('fechaEntrada', TextType::class)
      ->add('umAlmacen',TextType::class);

  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\productoAssets',
      'csrf_protection' => false,
      'csrf_field_name' => '_token',
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundleproducto_form_type';
  }
}
