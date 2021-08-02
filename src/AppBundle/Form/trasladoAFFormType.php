<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class trasladoAFFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('respEntrega', TextType::class, ['label' => 'Responsable de entrega:','data'=>''])
            ->add('areaEntrega', TextType::class, ['label' => 'Area de entrega:','data'=>''])
            ->add('receptor', TextType::class, ['label' => 'Receptor:','data'=>''])
            ->add('areaDestino', EntityType::class, ['label' => 'Área Destino:','placeholder' => 'Seleccione', 'class' => 'AppBundle\Entity\departamento'])
            ->add('autorizado', TextType::class, ['label' => 'Autorizado:','data'=>'Bárbara S. López'])
            ->add('respAFT', TextType::class, ['label' => 'Responsable AFT:','data'=>''])
            ->add('aprobado', TextType::class, ['label' => 'Aprobado:','data'=>'Dr. Lázaro Pérez']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'AppBundle\Entity\movimientoI_AF']);
    }

    public function getBlockPrefix()
    {
        return 'app_bundletraslado_afform_type';
    }
}
