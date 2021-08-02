<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 3/11/2019
 * Time: 8:52 AM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="centroCostoRepository")
 * @ORM\Table(name="centro_costo")
 */
class centroCosto
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
  private $id;
  /**
   * @ORM\Column(type="string")
   */
  private $idCentroCosto;

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
  public function getIdCentroCosto()
  {
    return $this->idCentroCosto;
  }

  /**
   * @param mixed $idCentroCosto
   */
  public function setIdCentroCosto($idCentroCosto)
  {
    $this->idCentroCosto = $idCentroCosto;
  }


}