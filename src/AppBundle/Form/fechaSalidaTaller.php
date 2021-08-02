<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class fechaSalidaTaller extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
  $builder
    ->add('idPeriferico')
    ->add('tipoPeriferico')
    ->add(
      'fechaSalida',
      DateType::class,   [
        'widget' => 'single_text',
      'format' => 'dd/MM/yyyy',
        'attr' => [
          'class' => 'js-datepicker'
          ],
        'html5' => false,
        ]);
   }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\taller'
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundlefecha_salida_taller';
  }
}
