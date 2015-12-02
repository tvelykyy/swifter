<?php

namespace Swifter\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups as SER;


/**
 * @ORM\Entity
 * @ORM\Table(name="template")
 */
class Template
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP})
     *
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value=0)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP})
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP})
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=100)
     */
    protected $path;

    /**
     * @ORM\Column(name="is_for_page", type="boolean")
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP})
     * @Assert\Type(type="integer")
     */
    protected $isForPage;

    public function getId()
    {
        return $this->id;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setForPage($isForPage)
    {
        $this->isForPage = $isForPage;
    }

    public function isForPage()
    {
        return $this->isForPage;
    }
}
