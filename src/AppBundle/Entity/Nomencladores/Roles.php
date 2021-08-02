<?php

namespace AppBundle\Entity\Nomencladores;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAsset;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Roles
 *
 * @ORM\Table(name="roles")
 * @UniqueEntity(fields="rol", message="Este Rol ya esta utilizado")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RolesRepository")
 */
class Roles implements RoleInterface
{
  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $rol
   *
   * @ORM\Column(name="rol", type="string")
   * @Assert\Type(type="string")
   * @Assert\NotBlank(message="Este campo es requerido")
   */
  private $rol;

  /**
   * @var \AppBundle\Entity\Nomencladores\GrupoUsuario
   *
   * @ORM\ManyToMany(targetEntity="GrupoUsuario", mappedBy="roles")
   */
  private $grupos;
    /**
     * @var string $rol
     *
     * @ORM\Column(name="detalles", type="string")
     * @Assert\Type(type="string")
     *
     */
    private $detalles;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->grupos = new ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set rol
   *
   * @param string $rol
   *
   * @return Roles
   */
  public function setRol($rol)
  {
    $this->rol = $rol;

    return $this;
  }

  /**
   * Get rol
   *
   * @return string
   */
  public function getRol()
  {
    return $this->rol;
  }

  /**
   * Add grupo
   *
   * @param \AppBundle\Entity\Nomencladores\GrupoUsuario $grupo
   *
   * @return Roles
   */
  public function addGrupo(\AppBundle\Entity\Nomencladores\GrupoUsuario $grupo)
  {
    $this->grupos[] = $grupo;

    return $this;
  }

  /**
   * Remove grupo
   *
   * @param \AppBundle\Entity\Nomencladores\GrupoUsuario $grupo
   */
  public function removeGrupo(\AppBundle\Entity\Nomencladores\GrupoUsuario $grupo)
  {
    $this->grupos->removeElement($grupo);
  }

  /**
   * Get grupos
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getGrupos()
  {
    return $this->grupos;
  }

  /**
   * Returns the role.
   *
   * This method returns a string representation whenever possible.
   *
   * When the role cannot be represented with sufficient precision by a
   * string, it should return null.
   *
   * @return string|null A string representation of the role, or null
   */
  public function getRole()
  {
    return $this->rol;
  }

    /**
     * @return string
     */
    public function getDetalles()
    {
        return $this->detalles;
    }

    /**
     * @param string $detalles
     */
    public function setDetalles($detalles)
    {
        $this->detalles = $detalles;
    }

}
