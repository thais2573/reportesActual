<?php

namespace AppBundle\Form;

use AppBundle\Repository\UsuarioRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class movimientoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('respEntrega', TextType::class, ['label' => 'Responsable de entrega:'])
           ->add('areaEntrega', EntityType::class, ['label' => 'Departamento de Entrega:', 'class' => 'AppBundle\Entity\departamento'])
            ->add('receptor', TextType::class, ['label' => 'Receptor:','data'=>'Elio Mendoza'])
          ->add('areaDestino', TextType::class, ['label' => 'Área Destino:','data'=>'Taller TECUN'])
            ->add('tipoMovimiento', TextType::class, ['label'=>'Tipo de Movimiento:'])
            ->add('autorizado', TextType::class, ['label' => 'Autorizado:','data'=>'Bárbara S. López'])
             ->add('respAFT', TextType::class, ['label' => 'Responsable AFT:','data'=>''])
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
        return 'app_bundlemovimiento_form_type';
    }
}
