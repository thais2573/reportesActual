<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 11/03/2019
 * Time: 22:34
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="movimientoI_AFRepository")
 * @ORM\Table(name="movimiento_AF")
 */
class movimientoI_AF
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     *@Assert\NotBlank(message="Debe seleccionar el responsable de la entrega")
     */
    private $respEntrega;
    /**
     * @ORM\Column(type="string")
     */
    private $areaEntrega;
    /**
     * @ORM\Column(type="string")
     *@Assert\NotBlank(message="Debe seleccionar el receptor")
     */
    private $receptor;
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe seleccionar el area de destino")
     *
     */
    private $areaDestino;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Debe seleccionar el tecnico encargado del movimiento")
     */
    private $tecnico;
    /**
     * @ORM\Column(type="string")
     */
    private $autorizado;
    /**
     * @ORM\Column(type="string")
     */
    private $aprobado;
    /**
     * @ORM\Column(type="string")
     */
    private $periferico;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\inventario")
     */
    private $inventario;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\incidencia")
     */
    private $incidencia;
  /**
   * @ORM\Column(type="string")
   * @Assert\NotBlank(message="Debe seleccionar el responsable AFT")
   */
  private $respAFT;
    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
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