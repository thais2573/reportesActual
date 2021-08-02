<?php

namespace AppBundle\Form;

use AppBundle\Entity\solicitud;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class solicitudProductoFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
  $builder
    ->add('idProducto', EntityType::class, array(
      'class' => 'AppBundle\Entity\productoAssets',
      'label' => 'Producto',
      'required' => true,
      'query_builder' => function (EntityRepository $er) {
        return $er->getProductosEnExistencia();
      },
      'attr' => array('class' => 'ser')
    ))
//      ->add('idProducto', ChoiceTy::class, array(
//        //  'class' => 'AppBundle\Entity\productoAssets',
//          'label' => 'Producto',
//          'required' => true,
////          'query_builder' => function (EntityRepository $er) {
////              return $er->getAllProductos();
////          },
////          'attr' => array('class' => 'ser')
//      ))
    ->add('um',TextType::class)
    ->add('cantidad',IntegerType::class);
  //->add('existencia',TextType::class);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\productoSolicitado',
      'csrf_protection' => false,
      'csrf_field_name' => '_token',
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundlesolicitud_producto_form_type';
  }
}
