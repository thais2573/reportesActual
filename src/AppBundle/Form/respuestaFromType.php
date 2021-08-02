<?php

namespace AppBundle\Form;

use AppBundle\Entity\equipoRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class respuestaFromType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('respuesta', TextType::class, ['label' => 'Respuesta'])
      ->add('tipoMov', ChoiceType::class, ['choices' => ['Solucionado' => 'Solucionado', 'Reparación' => 'Reparación',
      ],'expanded' => true, 'label' => 'Acciones'])
      ->add('asesorio', TextType::class, ['label' => 'Periferico:','data'=>'']);



  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(['data_class' => 'AppBundle\Entity\incidencia']);
  }

  public function getBlockPrefix()
  {
    return 'app_bundlerespuesta_from_type';
  }
}
