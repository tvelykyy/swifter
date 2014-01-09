<?php

namespace Swifter\FrontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="pageBlocks")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     **/
    protected $page;

    /**
     * @ORM\ManyToOne(targetEntity="Block")
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     **/
    protected $block;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

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
