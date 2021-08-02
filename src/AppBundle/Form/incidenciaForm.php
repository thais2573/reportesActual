<?php

namespace AppBundle\Form;

use Doctrine\DBAL\Types\DateTimeType;
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


class incidenciaForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo', EntityType::class, ['placeholder' => 'Seleccione', 'class' => 'AppBundle\Entity\tipo', 'choice_label' => 'name', 'label' => 'Categoría*'])
            ->add('dpto', EntityType::class, ['label' => 'Departamento','class' => 'AppBundle\Entity\departamento','placeholder'=>'Seleccione','choice_label' => 'name'])
            ->add('asunto', TextType::class, ['label' => 'Asunto'])
            ->add('resumen', TextareaType::class, ['label' => 'Resumen'])
           ->add('fecha', DateType::class, array('label'  => 'Fecha de Mantenimiento :', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd'))
            ->add('inventario', EntityType::class, ['label' => 'Estación de Trabajo', 'class' => 'AppBundle\Entity\inventario', 'choice_label' => 'centroCosto', 'multiple' => TRUE]);
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
        return 'app_bundleincidencia_form';
    }
}
