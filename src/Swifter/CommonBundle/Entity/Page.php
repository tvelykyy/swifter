<?php

namespace Swifter\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;

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
     * @Groups({"basic", "list", "details", "page-no-parent-template"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Groups({"basic", "list", "details", "page-no-parent-template"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=200)
     *
     * @Groups({"list", "details", "page-no-parent-template"})
     */
    protected $uri;

    /**
     * @ORM\OneToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     *
     * @Groups({"details"})
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="PageBlock", mappedBy="page", cascade={"all"})
     * @SerializedName("pageBlocks")
     * @Groups({"details", "page-no-parent-template"})
     **/
    protected $pageBlocks;

    /**
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     *
     * @Groups({"list", "details"})
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
