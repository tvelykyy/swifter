<?php

namespace Swifter\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="role")
 */
class Role implements RoleInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=30, unique=true, nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=100, unique=true, nullable=false)
     */
    protected $description;

    public function getId()
    {
        return $this->id;
    }

    public function getRole()
    {
        return $this->title;
    }

    public function setRole($role)
    {
        $this->title = $role;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

}