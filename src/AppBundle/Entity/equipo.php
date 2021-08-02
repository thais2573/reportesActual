<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * equipo
 *
 * @ORM\Entity(repositoryClass="equipoRepository")
 * @ORM\Table(name="equipo")
 */
class equipo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;


  /**
   * @ORM\Column(type="string" ,nullable=false,unique=true)
   */
  private $numInventario;
  /**
   * @ORM\Column(type="string",nullable=false)
   */
  private $modelo;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $serie;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $marca;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $estado;
    /**
     * @var equipo
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\departamento", inversedBy="lista_equipos",cascade={"persist"})
     * @ORM\JoinColumn(name="departamento_id", referencedColumnName="id" , nullable=true, onDelete="SET NULL")
     */
    private $departamento;
  /**
   * @var equipo
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\inventario", inversedBy="equipos",cascade={"persist"})
   * @ORM\JoinColumn(name="estacion_id", referencedColumnName="id" , nullable=true, onDelete="SET NULL")
   */
  private $estacion;
  /**
   * @var \Doctrine\Common\Collections\Collection
   *
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\componente", mappedBy="cpu", cascade={"persist"})
   */
  private $componente;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\deuda", mappedBy="cpu", cascade={"persist","remove"})
     */
    private $deuda;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $sello;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $capacidad;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $color;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $fechaMantenimiento;
    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $fechaModificacion;

  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $tipo;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $tipoTonerCartucho;
  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $tonerCartucho;

  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $lcd;

  /**
   * @ORM\Column(type="string",nullable=true)
   */
  private $tipoEquipo;

  /**
   * Constructor
   */
  public function __construct()
  {
   // $this->componente = new ArrayCollection();
    $this->deuda = new ArrayCollection();
  }
  /**
   * Add componente.
   *
   * @param \AppBundle\Entity\componente $componente
   *
   * @return componente
   */
  public function addComponente(componente $componente)
  {
    $this->componente[] = $componente;

    return $this;
  }

  /**
   * Remove equipo.
   *
   * @param \AppBundle\Entity\componente $componente
   *
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeComponente($componente)
  {
    return $this->componente->removeElement($componente);
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
     * @return equipo
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * @param equipo $departamento
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;
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
   * @return mixed
   */
  public function getNumInventario()
  {
    return $this->numInventario;
  }

  /**
   * @param mixed $numInventario
   */
  public function setNumInventario($numInventario)
  {
    $this->numInventario = $numInventario;
  }

  /**
   * @return mixed
   */
  public function getModelo()
  {
    return $this->modelo;
  }

  /**
   * @param mixed $modelo
   */
  public function setModelo($modelo)
  {
    $this->modelo = $modelo;
  }

  /**
   * @return mixed
   */
  public function getSerie()
  {
    return $this->serie;
  }

  /**
   * @param mixed $serie
   */
  public function setSerie($serie)
  {
    $this->serie = $serie;
  }

  /**
   * @return mixed
   */
  public function getMarca()
  {
    return $this->marca;
  }

  /**
   * @param mixed $marca
   */
  public function setMarca($marca)
  {
    $this->marca = $marca;
  }

  /**
   * @return mixed
   */
  public function getEstado()
  {
    return $this->estado;
  }

  /**
   * @param mixed $estado
   */
  public function setEstado($estado)
  {
    $this->estado = $estado;
  }

  /**
   * @return equipo
   */
  public function getEstacion()
  {
    return $this->estacion;
  }

  /**
   * @param equipo $estacion
   */
  public function setEstacion($estacion)
  {
    $this->estacion = $estacion;
  }

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getComponente()
  {
    return $this->componente;
  }

  /**
   * @param mixed $componente
   */
  public function setComponente($componente)
  {
    $this->componente = $componente;
  }



  /**
   * @return mixed
   */
  public function getSello()
  {
    return $this->sello;
  }

  /**
   * @param mixed $sello
   */
  public function setSello($sello)
  {
    $this->sello = $sello;
  }

  /**
   * @return mixed
   */
  public function getCapacidad()
  {
    return $this->capacidad;
  }

  /**
   * @param mixed $capacidad
   */
  public function setCapacidad($capacidad)
  {
    $this->capacidad = $capacidad;
  }

  /**
   * @return mixed
   */
  public function getColor()
  {
    return $this->color;
  }

  /**
   * @param mixed $color
   */
  public function setColor($color)
  {
    $this->color = $color;
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

  /**
   * @return mixed
   */
  public function getTipo()
  {
    return $this->tipo;
  }

  /**
   * @param mixed $tipo
   */
  public function setTipo($tipo)
  {
    $this->tipo = $tipo;
  }

  /**
   * @return mixed
   */
  public function getTipoTonerCartucho()
  {
    return $this->tipoTonerCartucho;
  }

  /**
   * @param mixed $tipoTonerCartucho
   */
  public function setTipoTonerCartucho($tipoTonerCartucho)
  {
    $this->tipoTonerCartucho = $tipoTonerCartucho;
  }

  /**
   * @return mixed
   */
  public function getTonerCartucho()
  {
    return $this->tonerCartucho;
  }

  /**
   * @param mixed $tonerCartucho
   */
  public function setTonerCartucho($tonerCartucho)
  {
    $this->tonerCartucho = $tonerCartucho;
  }

  /**
   * @return mixed
   */
  public function getLcd()
  {
    return $this->lcd;
  }

  /**
   * @param mixed $lcd
   */
  public function setLcd($lcd)
  {
    $this->lcd = $lcd;
  }

  /**
   * @return mixed
   */
  public function getTipoEquipo()
  {
    return $this->tipoEquipo;
  }

  /**
   * @param mixed $tipoEquipo
   */
  public function setTipoEquipo($tipoEquipo)
  {
    $this->tipoEquipo = $tipoEquipo;
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
     * @return mixed
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * @param mixed $fechaModificacion
     */
    public function setFechaModificacion($fechaModificacion)
    {
        $this->fechaModificacion = $fechaModificacion;
    }




}
