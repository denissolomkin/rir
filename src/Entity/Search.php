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
     * @var MetaKeyword[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MetaKeyword", cascade={"persist"})
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
     * @var MetaPurpose
     *
     * @ORM\ManyToOne(targetEntity="MetaPurpose")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $purpose;

    /**
     * @var MetaDocumentType
     *
     * @ORM\ManyToOne(targetEntity="MetaDocumentType")
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
     * @var MetaExtension
     *
     * @ORM\ManyToOne(targetEntity="MetaExtension")
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
     * @var MetaMedia
     *
     * @ORM\ManyToOne(targetEntity="MetaMedia")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $mediaType;

    // SEARCH

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $category;

    /**
     * @var MetaAccessLevel
     *
     * @ORM\ManyToOne(targetEntity="MetaAccessLevel")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $accessLevel;

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
     * @ORM\ManyToMany(targetEntity="User")
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
     * @return MetaPurpose
     */
    public function getPurpose(): ?MetaPurpose
    {
        return $this->purpose;
    }

    /**
     * @param MetaPurpose $purpose
     * @return self
     */
    public function setPurpose(MetaPurpose $purpose): self
    {
        $this->purpose = $purpose;
        return $this;
    }

    /**
     * @return MetaDocumentType
     */
    public function getDocumentType(): ?MetaDocumentType
    {
        return $this->documentType;
    }

    /**
     * @param MetaDocumentType $documentType
     * @return self
     */
    public function setDocumentType(MetaDocumentType $documentType): self
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
    public function getExtension(): ?MetaExtension
    {
        return $this->extension;
    }

    /**
     * @param MetaExtension $extension
     * @return self
     */
    public function setExtension(MetaExtension $extension): self
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
     * @return MetaMedia
     */
    public function getMediaType(): ?MetaMedia
    {
        return $this->mediaType;
    }

    /**
     * @param MetaMedia $mediaType
     * @return self
     */
    public function setMediaType(MetaMedia $mediaType): self
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
     * @return MetaAccessLevel
     */
    public function getAccessLevel(): ?MetaAccessLevel
    {
        return $this->accessLevel;
    }

    /**
     * @param MetaAccessLevel $accessLevel
     * @return self
     */
    public function setAccessLevel(MetaAccessLevel $accessLevel): self
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
     * @return Search
     */
    public function setResourceId(int $resourceId): Search
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

    public function setAuthors(ArrayCollection $authors): self
    {
        $this->authors = $authors;
        return $this;
    }


}
