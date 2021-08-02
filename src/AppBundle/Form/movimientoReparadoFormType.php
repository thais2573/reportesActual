<?php

namespace AppBundle\Form;

use AppBundle\Entity\Administracion\Usuario;
use AppBundle\Repository\UsuarioRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class movimientoReparadoFormType extends AbstractType
{
  private $tokenStorage;

  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $builder
      // ->add('receptor', EntityType::class, ['placeholder' => 'Seleccione', 'class' => 'AppBundle\Entity\Administracion\Usuario','label' => 'Receptor:'])

      ->add('areaDestino',  TextType::class, ['label' => 'Área Destino:'])
      ->add('respEntrega', TextType::class, ['label' => 'Responsable de Entrega:', 'data' => ' Elio Mendoza'])
      ->add('areaEntrega', TextType::class, ['label' => 'Area que de Entrega:', 'data' => 'Taller TECUN'])
      ->add('tipoMovimiento', TextType::class, ['label' => 'Tipo de Movimiento:'])
      ->add('autorizado', TextType::class, ['label' => 'Autorizado:', 'data' => 'Bárbara S. López'])
      ->add('aprobado', TextType::class, ['label' => 'Aprobado:', 'data' => 'Dr. Lázaro Pérez'])
      ->add('respAFT', TextType::class, ['label' => 'Responsable AFT:', 'data' => ''])
      ->add('receptor', TextType::class, ['label' => 'Receptor:', 'data' => '']);
    //  );
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
    return 'app_bundlemovimientoR_form_type';
  }
}
