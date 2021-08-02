<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 2/19/2019
 * Time: 11:36 AM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="periferico")
 */
class periferico
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
  private $name;

  /**
   * @ORM\Column(type="string")
   */
  private $marca;

  /**
   * @ORM\Column(type="string")
   */
  private $serie;

  /**
   * @ORM\Column(type="string")
   */
  private $modelo;

  /**
   * @return mixed
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @param mixed $name
   */
  public function setName($name)
  {
    $this->name = $name;
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
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param mixed $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  public function __toString()
  {
    return $this->getName();
  }



}