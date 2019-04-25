<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\MappedSuperclass */
abstract class AbstractResource
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
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="resource.blank_annotation")
     * @Assert\Length(min=10, minMessage="resource.too_short_annotation")
     */
    protected $annotation;

    /**
     * @var ResourceKeyword[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ResourceKeyword", cascade={"persist"})
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="40", maxMessage="resource.too_many_keywords")
     */
    protected $keywords;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $author;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $source;

    /**
     * @var ResourcePurpose
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourcePurpose")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $purpose;

    /**
     * @var ResourceDocumentType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourceDocumentType")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $documentType;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $theme;

    // TECH


    /**
     * @var ResourceExtension
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourceExtension")
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank
     */
    protected $extension;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    protected $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $editedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $approvedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $expiredAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $language;

    /**
     * @var ResourceMediaType
     *
     * @ORM\ManyToOne(targetEntity="ResourceMediaType")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $mediaType;

    // SEARCH

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $category;

    /**
     * @var ResourceAccessLevel
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourceAccessLevel")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $accessLevel;

    /**
     * Resource constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->editedAt = new \DateTime();

        $this->keywords = new ArrayCollection();
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
     * @return ResourceKeyword[]|ArrayCollection
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    /**
     * @param ResourceKeyword[]|ArrayCollection $keywords
     * @return self
     */
    public function addKeyword(ResourceKeyword ...$keywords): self
    {
        foreach ($keywords as $keyword) {
            if (!$this->keywords->contains($keyword)) {
                $this->keywords->add($keyword);
            }
        }
        return $this;
    }

    /**
     * @param ResourceKeyword $keyword
     * @return self
     */
    public function removeKeyword(ResourceKeyword $keyword): self
    {
        $this->keywords->removeElement($keyword);
        return $this;
    }


    /**
     * @return User
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return self
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;
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
     * @return ResourcePurpose
     */
    public function getPurpose(): ?ResourcePurpose
    {
        return $this->purpose;
    }

    /**
     * @param ResourcePurpose $purpose
     * @return self
     */
    public function setPurpose(ResourcePurpose $purpose): self
    {
        $this->purpose = $purpose;
        return $this;
    }

    /**
     * @return ResourceDocumentType
     */
    public function getDocumentType(): ?ResourceDocumentType
    {
        return $this->documentType;
    }

    /**
     * @param ResourceDocumentType $documentType
     * @return self
     */
    public function setDocumentType(ResourceDocumentType $documentType): self
    {
        $this->documentType = $documentType;
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
     * @return string
     */
    public function getExtension(): ?string
    {
        return $this->extension;
    }

    /**
     * @param ResourceExtension $extension
     * @return self
     */
    public function setExtension(ResourceExtension $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return self
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEditedAt(): \DateTime
    {
        return $this->editedAt;
    }

    /**
     * @param \DateTime $editedAt
     * @return self
     */
    public function setEditedAt(\DateTime $editedAt): self
    {
        $this->editedAt = $editedAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTime $publishedAt
     * @return self
     */
    public function setPublishedAt(\DateTime $publishedAt): ?self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }


    /**
     * @return string
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return self
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return ResourceMediaType
     */
    public function getMediaType(): ?ResourceMediaType
    {
        return $this->mediaType;
    }

    /**
     * @param ResourceMediaType $mediaType
     * @return self
     */
    public function setMediaType(ResourceMediaType $mediaType): self
    {
        $this->mediaType = $mediaType;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return self
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return ResourceAccessLevel
     */
    public function getAccessLevel(): ?ResourceAccessLevel
    {
        return $this->accessLevel;
    }

    /**
     * @param ResourceAccessLevel $accessLevel
     * @return self
     */
    public function setAccessLevel(ResourceAccessLevel $accessLevel): self
    {
        $this->accessLevel = $accessLevel;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedAt(): ?\DateTime
    {
        return $this->approvedAt;
    }

    /**
     * @param \DateTime $approvedAt
     * @return self
     */
    public function setApprovedAt(\DateTime $approvedAt): self
    {
        $this->approvedAt = $approvedAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiredAt(): ?\DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @param \DateTime $expiredAt
     * @return self
     */
    public function setExpiredAt(\DateTime $expiredAt = null): self
    {
        $this->expiredAt = $expiredAt;
        return $this;
    }


}
