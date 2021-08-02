<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 05/02/2019
 * Time: 14:39
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="solicitudRepository")
 * @ORM\Table(name="solicitud")
 */
class solicitud
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;
  /**
   * @var departamento
   *
   * @ORM\ManyToOne(targetEntity="AppBundle\Entity\departamento",inversedBy="solicitud_material")
   * @ORM\JoinColumn(name="centroCosto_id",referencedColumnName="id")
   */
  private $centroCosto;
  /**
   * @ORM\Column(type="string")
   */
  private $codigo;
  /**
   * @var \Doctrine\Common\Collections\Collection
   *
   * @ORM\OneToMany(targetEntity="AppBundle\Entity\productoSolicitado", mappedBy="solicitud", cascade={"persist","remove"})
   */
  private $material;
  /**
   * @ORM\Column(type="datetime")
   */
  private $fechaSolicitud;
  /**
   * @ORM\Column(type="string")
   */
  private $solicitante;
    /**
     * @ORM\Column(type="string")
     */
    private $numeroSolicitud;
  public function __construct()
  {
    $this->material = new ArrayCollection();
  }
  /**
   * Add productoAssets.
   *
   * @param \AppBundle\Entity\productoAssets $material
   *
   * @return productoAssets
   */
  public function addProducto($material)
  {
    $this->material[] = $material;

    return $this;
  }

  /**
   * Remove productoAssets.
   *
   * @param \AppBundle\Entity\productoAssets $material
   *
   * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
   */
  public function removeProducto($material)
  {
    return $this->material->removeElement($material);
  }

  /**
   * @return mixed
   */
  public function getCentroCosto()
  {
    return $this->centroCosto;
  }

  /**
   * @param mixed $centroCosto
   */
  public function setCentroCosto($centroCosto)
  {
    $this->centroCosto = $centroCosto;
  }

  /**
   * @return mixed
   */
  public function getCodigo()
  {
    return $this->codigo;
  }

  /**
   * @param mixed $codigo
   */
  public function setCodigo($codigo)
  {
    $this->codigo = $codigo;
  }

  /**
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getMaterial()
  {
    return $this->material;
  }

  /**
   * @param mixed $material
   */
  public function setMaterial($material)
  {
    $this->material = $material;
  }

    /**
     * @return mixed
     */
    public function getNumeroSolicitud()
    {
        return $this->numeroSolicitud;
    }

    /**
     * @param mixed $numeroSolicitud
     */
    public function setNumeroSolicitud($numeroSolicitud)
    {
        $this->numeroSolicitud = $numeroSolicitud;
    }

  /**
   * @return mixed
   */
  public function getUm()
  {
    return $this->um;
  }

  /**
   * @param mixed $um
   */
  public function setUm($um)
  {
    $this->um = $um;
  }

  /**
   * @return mixed
   */
  public function getCantidad()
  {
    return $this->cantidad;
  }

  /**
   * @param mixed $cantidad
   */
  public function setCantidad($cantidad)
  {
    $this->cantidad = $cantidad;
  }


  /**
   * @return mixed
   */
  public function getFechaSolicitud()
  {
    return $this->fechaSolicitud;
  }

  /**
   * @param mixed $fechaSolicitud
   */
  public function setFechaSolicitud($fechaSolicitud)
  {
    $this->fechaSolicitud = $fechaSolicitud;
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
  public function getSolicitante()
  {
    return $this->solicitante;
  }

  /**
   * @param mixed $solicitante
   */
  public function setSolicitante($solicitante)
  {
    $this->solicitante = $solicitante;
  }


}