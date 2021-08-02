<?php

namespace AppBundle\Form;

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

class trasladoSIFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('respEntrega', TextType::class, ['label' => 'Responsable de entrega:','data'=>''])
            ->add('areaEntrega', EntityType::class, ['label' => 'Área Entrega:','placeholder' => 'Seleccione', 'class' => 'AppBundle\Entity\departamento'])
            ->add('receptor', TextType::class, ['label' => 'Receptor:','data'=>''])
            ->add('areaDestino', TextType::class, ['label' => 'Área Destino:'])
            ->add('autorizado', TextType::class, ['label' => 'Autorizado:','data'=>'Bárbara S. López'])
            ->add('respAFT', TextType::class, ['label' => 'Responsable AFT:','data'=>''])
            ->add('aprobado', TextType::class, ['label' => 'Aprobado:','data'=>'Dr. Lázaro Pérez']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'AppBundle\Entity\movimientoI']);
    }

    public function getBlockPrefix()
    {
        return 'app_bundletrasladoSIform_type';
    }
}
