<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class entregaTallerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
$builder
    ->add('fechaInicio', DateType::class, array('label'  => 'Fecha de Inicio :', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd'))
    ->add('fechaFin', DateType::class,array('label'  => 'Fecha de Fin :', 'widget' => 'single_text', 'format' => 'yyyy-MM-dd'))
    ->add('deudas', CheckboxType::class, [
        'label'    => 'Partes y piezas',
        'required' => false,
    ])  ->add('mantenimiento', CheckboxType::class, [
        'label'    => 'Mantenimiento',
        'required' => false,
    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $defaultData = array('message' => 'Type your message here');
    }

    public function getBlockPrefix()
    {
        return 'app_bundleentrega_taller_form_type';
    }
}
