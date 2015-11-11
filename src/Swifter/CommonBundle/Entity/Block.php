<?php

namespace Swifter\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups as SER;

/**
 * @ORM\Entity
 * @ORM\Table(name="block")
 */
class Block
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP, SER::PAGE_BASIC_GROUP})
     *
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value=0)
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=50)
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP, SER::PAGE_BASIC_GROUP})
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=50)
     */
    protected $title;

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

}