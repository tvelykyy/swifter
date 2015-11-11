<?php

namespace Swifter\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups as SER;

/**
 * @ORM\Entity
 * @ORM\Table(name="page")
 */
class Page
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({SER::BASIC_GROUP, SER::LIST_GROUP, SER::DETAILS_GROUP, SER::PAGE_BASIC_GROUP})
     *
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value=0)
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Groups({SER::BASIC_GROUP, SER::LIST_GROUP, SER::DETAILS_GROUP, SER::PAGE_BASIC_GROUP})
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=200)
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP, SER::PAGE_BASIC_GROUP})
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min=1, max=200)
     */
    protected $uri;

    /**
     * @ORM\OneToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     *
     * @Groups(SER::DETAILS_GROUP)
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="PageBlock", mappedBy="page", cascade={"all"})
     * @SerializedName("pageBlocks")
     *
     * @Groups({SER::DETAILS_GROUP, SER::PAGE_BASIC_GROUP})
     **/
    protected $pageBlocks;

    /**
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     *
     * @Groups({SER::LIST_GROUP, SER::DETAILS_GROUP})
     **/
    protected $template;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setPageBlocks($pageBlocks)
    {
        $this->pageBlocks = $pageBlocks;
    }

    public function getPageBlocks()
    {
        return $this->pageBlocks;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }
}
