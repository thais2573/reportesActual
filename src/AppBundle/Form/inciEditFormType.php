<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class inciEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    { $builder
        ->add('tipo', TextType::class, ['label'=>'Categoría*'
        ])
        ->add('dpto', TextType::class,['label'=>'Departamento'])
        ->add('asunto', TextType::class,['label'=>'Asunto'])
        ->add('resumen', TextareaType::class,['label'=>'Resumen'])
        ->add('respuesta', TextType::class, ['label'=>'Respuesta'])
        ->add('fecha', TextType::class,['label'=>'Fecha'])
        ->add('estado', TextType::class, ['label' => 'Estado*'
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'AppBundle\Entity\incidencia',
        'csrf_protection' => false,
        'csrf_field_name' => '_token',
        // a unique key to help generate the secret token

      ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundleinci_edit_form_type';
    }
}
