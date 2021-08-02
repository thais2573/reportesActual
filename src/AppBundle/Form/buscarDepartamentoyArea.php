<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class buscarDepartamentoyArea extends AbstractType
{
  private $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    parent:: buildForm($builder, $options);
    $builder->resetModelTransformers();
    $builder->resetViewTransformers();


    $builder
      ->add('centroCosto', TextType::class)
      ->add('inventario', TextType::class)
      ->add('dpto', ChoiceType::class);


    // $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'onPreSetData'));
    // $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
  }

  protected function addElements(FormInterface $form, $cCosto = null, $idcosto)
  {
    // 4. Add the province element
    $form->add('centro_costo', ChoiceType::class, array(
      'required' => true,
      'data' => $cCosto,
      'placeholder' => 'Selecciona el area...',
    ));

    // Neighborhoods empty, unless there is a selected City (Edit View)
    $departamentos = array();
    $idcosto = 0;
    // If there is a city stored in the Person entity, load the neighborhoods of it
    if ($cCosto) {
      // Fetch Neighborhoods of the City if there's a selected city
      /**
       * Configurar conexion al assets desde Windows (Liuben)
       */
      if ($cCosto) {
        $serverName = "premium.camilo.sld.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = 'Super2009';
        try {
          $coneccionAssets = new \PDO(
            "sqlsrv:server=$serverName;Database=$database",
            $uid,
            $pwd,
            array(
              //PDO::ATTR_PERSISTENT => true,
              \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            )
          );

          $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
          $query = $coneccionAssets->query($sql);
          if ($query) {
            //$result = array();
            while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
              $departamentos[] = $var;
            }
            /* foreach ($result as $depart) {
               dump($depart['Desc_AreaResponsabilidad']);
             }*/
          }


        } catch (\PDOException $e) {
          die("No se conecta con el servidor! - " . $e->getMessage());
        }

      }
      // Add the Neighborhoods field with the properly data
      $form->add('departamento', ChoiceType::class, array(
        'required' => true,
        'placeholder' => 'Selecciona un area primero ...',
        'choices' => $departamentos
      ));
    }
  }

  function onPreSubmit(FormEvent $event)
  {
    $form = $event->getForm();
    $data = $event->getData();

    /**
     * Configurar conexion al assets desde Windows (Liuben)
     */
    $serverName = "premium.camilo.sld.cu";
    $database = "RETINOSIS";
    $uid = 'user_assetsp';
    $pwd = 'Super2009';
    try {
      $coneccionAssets = new \PDO(
        "sqlsrv:server=$serverName;Database=$database",
        $uid,
        $pwd,
        array(
          //PDO::ATTR_PERSISTENT => true,
          \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        )
      );

      /**
       * Consulta para obtener los centros de costos
       */
      $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
      $query = $coneccionAssets->query($sql);
      if ($query) {
        $result = array();
        while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
          $result[] = $var;
        }
        // foreach ($result as $centro) {
        //  dump($centro['Desc_Ccosto']);
        // }

      }
    } catch (\PDOException $e) {
      die("No se conecta con el servidor! - " . $e->getMessage());
    }

    $idcosto = $result['Id_Ccosto'];
    $this->addElements($form, $result['Desc_Ccosto'], $idcosto);
  }

  function onPreSetData(FormEvent $event)
  {
    $person = $event->getData();
    $form = $event->getForm();

    // When you create a new person, the City is always empty
    $cCosto = $person->getDepartamento() ? $person->getDepartamento() : null;
    // $municipio = $person->getMunicipio() ? $person->getMunicipio() : null;
    // $unidad = $person->getUnidad() ? $person->getUnidad() : null;
    $this->addElements($form, $cCosto, $departamento = null);

  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'AppBundle\Entity\inventario'
    ));
  }

  public function getBlockPrefix()
  {
    return 'app_bundlebuscar_departamentoy_area';
  }
}
