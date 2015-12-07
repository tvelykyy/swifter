<?php

namespace Swifter\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups as SER;

/**
 * @ORM\Entity
 * @ORM\Table(name="block_type")
 */
class BlockType
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({SER::PAGE_DETAILS_GROUP})
     *
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value=0)
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=20)
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP})
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=20)
     */
    protected $title;

    /**
     * @ORM\Column(name="constraint", type="string", length=30)
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP})
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=30)
     */
    protected $constraint;

    public function setId($id)
    {
        $this->id = $id;
    }
    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setConstraint($constraint)
    {
        $this->constraint = $constraint;
    }

    public function getConstraint()
    {
        return $this->constraint;
    }

}