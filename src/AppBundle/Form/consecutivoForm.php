<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class consecutivoForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('consecutivoSolicitud');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Administracion\Usuario',
                  'csrf_protection' => false,
                  'csrf_field_name' => '_token',
                  // a unique key to help generate the secret token

        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundleconsecutivo_form';

    }
}
