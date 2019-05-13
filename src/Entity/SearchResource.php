<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rir_search")
 */
class SearchResource extends AbstractResource
{

    // COMMON

    /**
     * @var int

     * @ORM\Column(type="integer")
     */
    private $resourceId;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $annotation;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $authors;

    /**
     * @var string[]
     *
     * @ORM\Column(type="simple_array", nullable=false)
     */
    protected $languages;


    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $category;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array", nullable=false)
     */
    protected $statuses;

    /**
     * Resource constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->languages = [];
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnnotation(): ?string
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     * @return self
     */
    public function setAnnotation(string $annotation): self
    {
        $this->annotation = $annotation;
        return $this;
    }

    /**
     * @return int
     */
    public function getResourceId(): ?int
    {
        return $this->resourceId;
    }

    /**
     * @param int $resourceId
     * @return SearchResource
     */
    public function setResourceId(int $resourceId): SearchResource
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getLanguages(): ?array
    {
        return $this->languages;
    }

    /**
     * @param string[] $languages
     * @return self
     */
    public function setLanguages(array $languages): self
    {
        $this->languages = $languages;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    /**
     * @param array $statuses
     * @return self
     */
    public function setStatuses(array $statuses): self
    {
        $this->statuses = $statuses;
        return $this;
    }

    public function getAuthors(): ?ArrayCollection
    {
        return $this->authors;
    }

    public function setAuthors(ArrayCollection $authors): AbstractResource
    {
        $this->authors = $authors;
        return $this;
    }


}
