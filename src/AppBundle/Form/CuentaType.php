<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CuentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, array(
                'label' => 'Ponga su password actual',
                'required' => true,
            ))
            ->add('password', RepeatedType::class, array(
                    "required" => "required",
                    'type' => PasswordType::class,
                    'invalid_message' => 'Los dos password deben coincidir',
                    'first_options' => array('label' => 'Password nuevo', "attr" => array("class" => "form-password form-control")),
                    'second_options' => array('label' => 'Repita el Password nuevo', "attr" => array("class" => "form-password form-control"))
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\ChangePassword'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_cuenta_type';
    }
    public function getName()
    {
        return 'change_passwd';
    }
}
