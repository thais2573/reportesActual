<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class buscarperifericoFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
   $builder
     ->add('perifericos',ChoiceType::class,array([
       'attr' => [' class' => ' form-control' ],'placeholder'=>'Seleccione el periferico'],
       'choises' => array(
         '0'=>'Seleccione',
         '1'=>'Backup',
         '2'=>'Bocinas',
         '3'=>'CPU Chasis',
         '4'=>'Impresora',
         '5'=>'Monitor',
         '6'=>'Scanner',
         '7'=>'Teclado',
         '8'=>'Mouse'


  ),'auto_initialize'=>true));


  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\periferico',
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundlebuscar_componente_form_type';
  }
}
