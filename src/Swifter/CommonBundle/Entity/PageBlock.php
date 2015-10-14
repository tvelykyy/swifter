<?php

namespace Swifter\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="page_block")
 */
class PageBlock
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Groups({"list", "details", "page-no-parent-template"})
     *
     * @Assert\Type(type="integer")
     * @Assert\GreaterThan(value=0)
     **/
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="pageBlocks")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     *
     **/
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Block")
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     *
     * @Groups({"list", "details", "page-no-parent-template"})
     **/
    protected $block;

    /**
     * @ORM\Column(type="text")
     *
     * @Groups({"list", "details", "page-no-parent-template"})
     **/
    protected $content;

    public function getId()
    {
        return $this->id;
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setBlock($block)
    {
        $this->block = $block;
    }

    public function getBlock()
    {
        return $this->block;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }
}
