<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class tipoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', TextType::class, ['label' => 'Categoría'])
            ->add('prioridad', ChoiceType::class, ['choices' => ['Alta'=>'Alta','Media' => 'Media','Baja'=>'Baja'
            ],
                'expanded' => false, 'label' => 'Prioridad','placeholder'=>'Seleccione'])
            ->add('permiso', ChoiceType::class, ['choices' => ['Si'=>'Si','No' => 'No'
            ],
                'expanded' => false, 'label' => 'Visibilidad Usuario','placeholder'=>'Seleccione']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'AppBundle\Entity\tipo']);
    }

    public function getBlockPrefix()
    {
        return 'app_bundletipo_form_type';
    }
}
