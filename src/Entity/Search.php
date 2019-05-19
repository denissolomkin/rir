<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Search
{


    // COMMON

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $source;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $theme;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @var MetaKeyword[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MetaKeyword", cascade={"persist"})
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="40", maxMessage="resource.too_many_keywords")
     */
    protected $keywords;

    /**
     * @var MetaPurpose[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MetaPurpose")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $purposes;

    /**
     * @var MetaDocumentType
     *
     * @ORM\ManyToMany(targetEntity="MetaDocumentType")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $documentTypes;


    // TECH


    /**
     * @var MetaExtension
     *
     * @ORM\ManyToMany(targetEntity="MetaExtension")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $extensions;

    /**
     * @var MetaMedia
     *
     * @ORM\ManyToMany(targetEntity="MetaMedia")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $mediaTypes;

    // SEARCH

    /**
     * @var MetaCategory
     *
     * @ORM\ManyToOne(targetEntity="MetaCategory")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $category;


    /**
     * @var User[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="User")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $authors;

    /**
     * @var string[]
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $languages;


    /**
     * Resource constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->authors = new ArrayCollection();
        $this->languages = [];

        $this->keywords = new ArrayCollection();
        $this->documentTypes = new ArrayCollection();
        $this->mediaTypes = new ArrayCollection();
        $this->extensions = new ArrayCollection();
        $this->purposes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return MetaKeyword[]|ArrayCollection
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    /**
     * @param MetaKeyword[]|ArrayCollection $keywords
     * @return self
     */
    public function addKeyword(MetaKeyword ...$keywords): self
    {
        foreach ($keywords as $keyword) {
            if (!$this->keywords->contains($keyword)) {
                $this->keywords->add($keyword);
            }
        }
        return $this;
    }

    /**
     * @param MetaKeyword $keyword
     * @return self
     */
    public function removeKeyword(MetaKeyword $keyword): self
    {
        $this->keywords->removeElement($keyword);
        return $this;
    }


    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return self
     */
    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }


    /**
     * @return MetaPurpose[]|ArrayCollection
     */
    public function getPurposes(): ?Collection
    {
        return $this->purposes;
    }

    /**
     * @param MetaPurpose[]|ArrayCollection $purposes
     * @return self
     */
    public function setPurposes(ArrayCollection $purposes): self
    {
        $this->purposes = $purposes;
        return $this;
    }

    /**
     * @return MetaDocumentType[]
     */
    public function getDocumentTypes(): ?Collection
    {
        return $this->documentTypes;
    }

    /**
     * @param MetaDocumentType[] $documentTypes
     * @return self
     */
    public function setDocumentTypes(ArrayCollection $documentTypes): self
    {
        $this->documentTypes = $documentTypes;
        return $this;
    }

    /**
     * @return string
     */
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     * @return self
     */
    public function setTheme(string $theme): self
    {
        $this->theme = $theme;
        return $this;
    }

    /**
     * @return MetaExtension[]
     */
    public function getExtensions(): ?Collection
    {
        return $this->extensions;
    }

    /**
     * @param MetaExtension[] $extensions
     * @return self
     */
    public function setExtensions(ArrayCollection $extensions): self
    {
        $this->extensions = $extensions;
        return $this;
    }


    /**
     * @return MetaMedia[]
     */
    public function getMediaTypes(): ?Collection
    {
        return $this->mediaTypes;
    }

    /**
     * @param MetaMedia[] $mediaTypes
     * @return self
     */
    public function setMediaType(ArrayCollection $mediaTypes): self
    {
        $this->mediaTypes = $mediaTypes;
        return $this;
    }

    /**
     * @return MetaCategory
     */
    public function getCategory(): ?MetaCategory
    {
        return $this->category;
    }

    /**
     * @param MetaCategory $category
     * @return self
     */
    public function setCategory(?MetaCategory $category): self
    {
        $this->category = $category;
        return $this;
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
     * @return ArrayCollection|null
     */
    public function getAuthors(): ?Collection
    {
        return $this->authors;
    }

    /**
     * @param ArrayCollection $authors
     * @return Search
     */
    public function setAuthors(ArrayCollection $authors): self
    {
        $this->authors = $authors;
        return $this;
    }


}
