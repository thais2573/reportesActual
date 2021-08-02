<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 05/02/2019
 * Time: 10:11
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="inventario")
 * @ORM\Entity(repositoryClass="inventarioRepository")
 * @UniqueEntity("ip",message="Esta direccion IP ya esta asignada a otra estacion")
 */
class inventario
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe seleccionar un responsable")
   */
  private $responsable;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe establecer una contraseña para el setup")
   */
  private $passSetup;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe llenar el campo Ip" )
   * @Assert\Ip(message="Formato incorrecto de IP")

   */
  private $ip;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe establecer un nombre de red")
   *
   */
  private $nombreRed;
  /**
   * @ORM\Column(type="string",nullable=true)
   * @Assert\NotBlank(message="Debe establecer un antivirus" )
   */
  private $antivirus;
  /**
   * @ORM\Column(type="string",nullable=true)
   *
   *
   */
  private $tecnico;
  /**
   * @ORM\Column(type="string",nullable=true)
   *
   */
  private $jefeInformatica;
  /**
   * @ORM\Column(type="string",nullable=true)
   *
   */
  private $estado;
  /**
   * @ORM\Column(type="string",nullable=false)
   *
   */
  private $activo;
  /**
   * @var departamento
   *
   *@ORM\ManyToOne(targetEntity="AppBundle\Entity\departamento",inversedBy="estacion")
   * @ORM\JoinColumn(name="centroCosto_id",referencedColumnName="id")
    * @Assert\NotBlank(message="Debe seleccionar el centro de costo" )
   */
  private $centroCosto;
  /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\equipo", mappedBy="estacion",cascade={"persist"})
   */
  private $equipos;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\deuda", mappedBy="estacion", cascade={"persist","remove"})
     */
    private $deuda;
  /**
  /**
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\equipo_de_inventarios", mappedBy="inventario",cascade={"persist"})
   */
  private $equipos_en_inventario;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\componente", mappedBy="estacion2", cascade={"persist"})
     */
    private $componente;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\componente_de_inventarios", mappedBy="inventarioC", cascade={"persist"})
     */
    private $componentes_en_inventarios;
  /**
   * @ORM\Column(type="datetime",nullable=true)
   * @Assert\DateTime()
   */
  private $fechacreacion;  /**
   * @ORM\Column(type="date",nullable=true)
   * @Assert\Date()
   */
  private $fechaMantenimiento;
  /**
   * Constructor
   */
  public function __construct()
  {
    $this->equipos = new ArrayCollection();
    $this->equipos_en_inventario = new ArrayCollection();
    $this->componente=new ArrayCollection();
    $this->componentes_en_inventarios=new ArrayCollection();
      $this->deuda = new ArrayCollection();
  }
    /**
     * Add deuda.
     *
     * @param \AppBundle\Entity\deuda $deuda
     *
     * @return deuda
     */
    public function addDeuda(deuda $deuda)
    {
        $this->deuda[] = $deuda;

        return $this;
    }
    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDeuda()
    {
        return $this->deuda;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $deuda
     */
    public function setDeuda($deuda)
    {
        $this->deuda = $deuda;
    }
    /**
     * Remove deuda.
     *
     * @param \AppBundle\Entity\deuda $deuda
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeDeuda($deuda)
    {
        return $this->deuda->removeElement($deuda);
    }
  /**
   * Get id.
   *
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }



  /**
   * Set responsable.
   *
   * @param string $responsable
   *
   * @return inventario
   */
  public function setResponsable($responsable)
  {
    $this->responsable = $responsable;

    return $this;
  }

  /**
   * Get responsable.
   *
   * @return string
   */
  public function getResponsable()
  {
    return $this->responsable;
  }

  /**
   * Set passSetup.
   *
   * @param string $passSetup
   *
   * @return inventario
   */
  public function setPassSetup($passSetup)
  {
    $this->passSetup = $passSetup;

    return $this;
  }

  /**
   * Get passSetup.
   *
   * @return string
   */
  public function getPassSetup()
  {
    return $this->passSetup;
  }

  /**
   * Set ip.
   *
   * @param string $ip
   *
   * @return inventario
   */
  public function setIp($ip)
  {
    $this->ip = $ip;

    return $this;
  }

  /**
   * Get ip.
   *
   * @return string
   */
  public function getIp()
  {
    return $this->ip;
  }

  /**
   * Set nombreRed.
   *
   * @param string $nombreRed
   *
   * @return inventario
   */
  public function setNombreRed($nombreRed)
  {
    $this->nombreRed = $nombreRed;

    return $this;
  }

  /**
   * Get nombreRed.
   *
   * @return string
   */
  public function getNombreRed()
  {
    return $this->nombreRed;
  }

  /**
   * Set antivirus.
   *
   * @param string|null $antivirus
   *
   * @return inventario
   */
  public function setAntivirus($antivirus = null)
  {
    $this->antivirus = $antivirus;

    return $this;
  }

  /**
   * Get antivirus.
   *
   * @return string|null
   */
  public function getAntivirus()
  {
    return $this->antivirus;
  }

  /**
   * Set tecnico.
   *
   * @param string|null $tecnico
   *
   * @return inventario
   */
  public function setTecnico($tecnico = null)
  {
    $this->tecnico = $tecnico;

    return $this;
  }

  /**
   * Get tecnico.
   *
   * @return string|null
   */
  public function getTecnico()
  {
    return $this->tecnico;
  }

  /**
   * Set jefeInformatica.
   *
   * @param string|null $jefeInformatica
   *
   * @return inventario
   */
  public function setJefeInformatica($jefeInformatica = null)
  {
    $this->jefeInformatica = $jefeInformatica;

    return $this;
  }

  /**
   * Get jefeInformatica.
   *
   * @return string|null
   */
  public function getJefeInformatica()
  {
    return $this->jefeInformatica;
  }

  /**
   * Set estado.
   *
   * @param string|null $estado
   *
   * @return inventario
   */
  public function setEstado($estado = null)
  {
    $this->estado = $estado;

    return $this;
  }

  /**
   * Get estado.
   *
   * @return string|null
   */
  public function getEstado()
  {
    return $this->estado;
  }

  /**
   * Set centroCosto.
   *
   * @param string $centroCosto
   *
   * @return inventario
   */
  public function setCentroCosto($centroCosto)
  {
    $this->centroCosto = $centroCosto;

    return $this;
  }

  /**
   * Get centroCosto.
   *
   * @return string
   */
  public function getCentroCosto()
  {
    return $this->centroCosto;
  }


  /**
   * Add equipo.
   *
   * @param \AppBundle\Entity\equipo $equipo
   *
   * @return inventario
   */
  public function addEquipo($equipo)
  {
    $this->equipos[] = $equipo;

    return $this;
  }

  /**
   * Remove equipo.
   *
   * @param \AppBundle\Entity\equipo $equipo
   *
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeEquipo($equipo)
  {
    return $this->equipos->removeElement($equipo);
  }



  /**
   * Get equipo.
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getEquipos()
  {
    return $this->equipos;
  }

  /**
   * @param mixed $equipos
   */
  public function setEquipos($equipos)
  {
    $this->equipos = $equipos;
  }

    /**
     * Add equipo.
     *
     * @param \AppBundle\Entity\componente $componente
     *
     * @return inventario
     */
    public function addComponente(\AppBundle\Entity\componente $componente)
    {
      //  $componente->setEstacion2($this);
        $this->componente[] = $componente;
      //  $this->addComponente($componente);
        return $this;
    }

    /**
     * Remove equipo.
     *
     * @param \AppBundle\Entity\componente $componente
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeComponente(\AppBundle\Entity\componente $componente)
    {
        return $this->componente->removeElement($componente);
    }


    /**
     * Get equipo.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComponente(){
        return $this->componente;
    }

    /**
     * @param mixed $equipos
     */
    public function setComponente($componente)
    {
        $this->componente = $componente;
    }


    /**
     * Get equipo.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComponenteInventario(){
        return $this->componentes_en_inventarios;
    }

    /**
     * @param mixed $equipos
     */
    public function setComponenteInventario($componenteI)
    {
        $this->componentes_en_inventarios = $componenteI;
    }


    /**
   * @return mixed
   */
  public function getFechacreacion()
  {
    return $this->fechacreacion;
  }

  /**
   * @param mixed $fechacreacion
   */
  public function setFechacreacion($fechacreacion)
  {
    $this->fechacreacion = $fechacreacion;
  }

  /**
   * @return mixed
   */
  public function getActivo()
  {
    return $this->activo;
  }

  /**
   * @param mixed $activo
   */
  public function setActivo($activo)
  {
    $this->activo = $activo;
  }

  /**
   * @return mixed
   */
  public function getEquiposEnInventario()
  {
    return $this->equipos_en_inventario;
  }

  /**
   * @param mixed $equipos_en_inventario
   */
  public function setEquiposEnInventario($equipos_en_inventario)
  {
    $this->equipos_en_inventario = $equipos_en_inventario;
  }

  /**
   * @return mixed
   */
  public function getFechaMantenimiento()
  {
    return $this->fechaMantenimiento;
  }

  /**
   * @param mixed $fechaMantenimiento
   */
  public function setFechaMantenimiento($fechaMantenimiento)
  {
    $this->fechaMantenimiento = $fechaMantenimiento;
  }

  public function __toString()
  {
    return $this->getNombreRed();
  }
}
