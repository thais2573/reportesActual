<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*->add('username', TextType::class, [
               'multiple' => true,
               'remote_route' => 'usuariosUMS',
               'class' => 'AppBundle\Entity\Administracion\Usuario',
               'primary_key' => 'id',
               'text_property' => 'name',
               'minimum_input_length' => 2,
               'page_limit' => 10,
               'allow_clear' => true,
               'delay' => 250,
               'cache' => true,
               'cache_timeout' => 60000, // if 'cache' is true
               'language' => 'en',
               'placeholder' => 'Selecciona los usuarios',
              'transformer' => '\Tetranz\Select2EntityBundle\Form\DataTransformer\EntitiesToPropertyTransformer',
               // 'object_manager' => $objectManager, // inject a custom object / entity manager
             ])*/
            ->add('username', TextType::class, array('label' => 'Nombre de usuario*', 'required' => false, 'attr' => array('placeholder' => 'nombre.apellido', 'class' => 'form-control')))
//
//            ->add('idAccout', TextType::class, array('label' => 'Id UMS', 'required' => false))
            ->add('estado', ChoiceType::class, array('choices' => array('Activo' => '1', 'Eliminado' => '2'), 'label' => 'Estado', 'required' => true))
//            ->add('ultimaConexion', DateTimeType::class, array('widget' => 'single_text', 'format' => 'dd/MM/yyyy HH:mm', 'attr' => array('class' => 'form-control'), 'required' => false))
//            ->add('password', PasswordType::class, array('label' => 'Contraseña*', 'required' => false, 'attr' => array('placeholder' => 'contraseña', 'class' => 'form-control')))
            ->add('grupos', EntityType::class, array('label'=>'Grupo(s)*', 'required' => false, 'class' => 'AppBundle:Nomencladores\GrupoUsuario', 'choice_label' => 'nombre', 'multiple' => 'multiple', 'placeholder' => '---Seleccione---'));
//            ->add('rol', ChoiceType::class, array('block_name' => 'rol', 'label' => 'Rol',
//                'placeholder' => 'Seleccione el rol',
//                'choices' => array(
//                    'Administrador' => 'ROLE_ADMIN',
//                    'Tecnico' => 'ROLE_TECNICO',
//                    'Usuario' => 'ROLE_USER',
//                    'Jefe de Departamento' => 'ROLE_JEFE_DEP',
//                    'AFT General' => 'ROLE_AFT',
//                )));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Administracion\Usuario'
        ));
    }
}
