<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 07/02/2019
 * Time: 11:00
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="movimientoRepository")
 * @ORM\Table(name="movimiento")
 */
class movimiento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe seleccionar el responsable de entrega")
     */
    private $respEntrega;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe llenar  la entidad que entrega")
     */
    private $entidadEntrega;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe llenar  la direccion que entrega")
     */
    private $direEntrega;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe seleccionar el area que entrega")
     */
    private $areaEntrega;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $receptor;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe seleccionar la entidad destino")
     */
    private $entidadDestino;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe seleccionar el area de destino")
     */
    private $direccionDestino;
    /**
     * @ORM\Column(type="string",unique=false)
     * @Assert\NotBlank()
     */
    private $areaDestino;
    /**
     * @ORM\Column(type="string")
     *
     */
    private $tipoMovimiento;
    /**
     * @ORM\Column(type="string",nullable=true)
     *
     */
    private $tecnico;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe llenar el personal autorizado")
     */
    private $autorizado;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe llenar el personal aprobado")
     */
    private $aprobado;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\inventario")
     */
    private $inventario;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\incidencia",cascade={"persist"})
     */
    private $incidencia;

    /**
     * @ORM\Column(type="string")
     */
    private $periferico;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaEntrega;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe llenar el tecnico encargado del movimiento")
   */
  private $respAFT;
    /**
     * @return mixed
     */
    public function getIncidencia()
    {
        return $this->incidencia;
    }

    /**
     * @param mixed $incidencia
     */
    public function setIncidencia($incidencia)
    {
        $this->incidencia = $incidencia;
    }

    /**
     * @return mixed
     */
    public function getRespEntrega()
    {
        return $this->respEntrega;
    }

    /**
     * @param mixed $respEntrega
     */
    public function setRespEntrega($respEntrega)
    {
        $this->respEntrega = $respEntrega;
    }

    /**
     * @return mixed
     */
    public function getEntidadEntrega()
    {
        return $this->entidadEntrega;
    }

    /**
     * @param mixed $entidadEntrega
     */
    public function setEntidadEntrega($entidadEntrega)
    {
        $this->entidadEntrega = $entidadEntrega;
    }

    /**
     * @return mixed
     */
    public function getDireEntrega()
    {
        return $this->direEntrega;
    }

    /**
     * @param mixed $direEntrega
     */
    public function setDireEntrega($direEntrega)
    {
        $this->direEntrega = $direEntrega;
    }

    /**
     * @return mixed
     */
    public function getAreaEntrega()
    {
        return $this->areaEntrega;
    }

    /**
     * @param mixed $areaEntrega
     */
    public function setAreaEntrega($areaEntrega)
    {
        $this->areaEntrega = $areaEntrega;
    }

    /**
     * @return mixed
     */
    public function getReceptor()
    {
        return $this->receptor;
    }

    /**
     * @param mixed $receptor
     */
    public function setReceptor($receptor)
    {
        $this->receptor = $receptor;
    }

    /**
     * @return mixed
     */
    public function getEntidadDestino()
    {
        return $this->entidadDestino;
    }

    /**
     * @param mixed $entidadDestino
     */
    public function setEntidadDestino($entidadDestino)
    {
        $this->entidadDestino = $entidadDestino;
    }

    /**
     * @return mixed
     */
    public function getDireccionDestino()
    {
        return $this->direccionDestino;
    }

    /**
     * @param mixed $direccionDestino
     */
    public function setDireccionDestino($direccionDestino)
    {
        $this->direccionDestino = $direccionDestino;
    }

    /**
     * @return mixed
     */
    public function getAreaDestino()
    {
        return $this->areaDestino;
    }

    /**
     * @param mixed $areaDestino
     */
    public function setAreaDestino($areaDestino)
    {
        $this->areaDestino = $areaDestino;
    }

    /**
     * @return mixed
     */
    public function getTipoMovimiento()
    {
        return $this->tipoMovimiento;
    }

    /**
     * @param mixed $tipoMovimiento
     */
    public function setTipoMovimiento($tipoMovimiento)
    {
        $this->tipoMovimiento = $tipoMovimiento;
    }

    /**
     * @return mixed
     */
    public function getTecnico()
    {
        return $this->tecnico;
    }

    /**
     * @param mixed $tecnico
     */
    public function setTecnico($tecnico)
    {
        $this->tecnico = $tecnico;
    }

    /**
     * @return mixed
     */
    public function getAutorizado()
    {
        return $this->autorizado;
    }

    /**
     * @param mixed $autorizado
     */
    public function setAutorizado($autorizado)
    {
        $this->autorizado = $autorizado;
    }

    /**
     * @return mixed
     */
    public function getAprobado()
    {
        return $this->aprobado;
    }

    /**
     * @param mixed $aprobado
     */
    public function setAprobado($aprobado)
    {
        $this->aprobado = $aprobado;
    }

    /**
     * @return mixed
     */
    public function getInventario()
    {
        return $this->inventario;
    }

    /**
     * @param mixed $inventario
     */
    public function setInventario($inventario)
    {
        $this->inventario = $inventario;
    }

    /**
     * @return mixed
     */
    public function getPeriferico()
    {
        return $this->periferico;
    }

    /**
     * @param mixed $periferico
     */
    public function setPeriferico($periferico)
    {
        $this->periferico = $periferico;
    }

    /**
     * @return mixed
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * @param mixed $fechaEntrega
     */
    public function setFechaEntrega($fechaEntrega)
    {
        $this->fechaEntrega = $fechaEntrega;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

  /**
   * @return mixed
   */
  public function getRespAFT()
  {
    return $this->respAFT;
  }

  /**
   * @param mixed $respAFT
   */
  public function setRespAFT($respAFT)
  {
    $this->respAFT = $respAFT;
  }


}