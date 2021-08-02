<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class GrupoUsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array('label' => 'Nombre de grupo*', 'required' => true, 'attr' => array('placeholder' => 'Nombre de grupo', 'class' => 'form-control')))
            ->add('descripcion', TextareaType::class, array('label' => 'Descripción', 'required' => false, 'attr' => array('placeholder' => 'Descripción', 'class' => 'form-control', 'style' => 'width: 400px; height: 80px;')))
            ->add('roles', EntityType::class, array('label'=>'Roles*', 'required' => false, 'class' => 'AppBundle:Nomencladores\Roles', 'choice_label' => 'rol', 'multiple' => true, 'placeholder' => '---Seleccione---'))
//            ->add('usuario')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Nomencladores\GrupoUsuario'
        ));
    }
}
