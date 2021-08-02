<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RolesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rol', TextType::class, array('label' => 'Nombre de usuario*', 'required' => true, 'attr' => array('placeholder' => 'ROLE_admin', 'class' => 'form-control')))
            ->add('grupos', EntityType::class, array('label'=>'Grupo(s)*', 'required' => false, 'class' => 'AppBundle:Nomencladores\GrupoUsuario', 'choice_label' => 'nombre', 'multiple' => true, 'placeholder' => '---Seleccione---'))
              ->add('detalles', TextareaType::class, array('label' => 'Detalles', 'required' => true, 'attr' => array('placeholder' => 'Este rol permite acceder al modulo X', 'class' => 'form-control')))
//
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Nomencladores\Roles'
        ));
    }
}
