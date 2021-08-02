<?php

namespace AppBundle\Form;

use AppBundle\AppBundle;
use AppBundle\Entity\productoSolicitado;
use AppBundle\Entity\solicitud;
use AppBundle\Entity\productoAssets;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class materialesSForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $builder
        ->add('centroCosto',EntityType::class,['class'=>'AppBundle\Entity\departamento'])
        ->add('material', CollectionType::class, array(
          'entry_type' => solicitudProductoFormType::class,
          'by_reference' => false,
          'allow_delete' => true,
          'allow_add' => true,
          'attr' => array('class' => productoSolicitado::class, 'style' => 'display:none'),
        ))

      ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
      $resolver->setDefaults(array(
        'data_class' => 'AppBundle\Entity\solicitud',
        'csrf_protection' => false,
        'csrf_field_name' => '_token',
      ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundlemateriales_sform';
    }
}
