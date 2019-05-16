<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResourceRepository")
 * @ORM\Table
 */
class Resource
{
    public const NUM_ITEMS = 10;

    public const STATUSES = [
        Resource::STATUS_DRAFT => 'resource.status.draft',
        Resource::STATUS_ON_MODERATION => 'resource.status.on_moderation',
        Resource::STATUS_PUBLISHED => 'resource.status.published',
        Resource::STATUS_DISABLED => 'resource.status.disabled',
        Resource::STATUS_DELETED => 'resource.status.deleted',
    ];

    public const STATUS_DRAFT = 0;
    public const STATUS_ON_MODERATION = 1;
    public const STATUS_PUBLISHED = 5;
    public const STATUS_DISABLED = 8;
    public const STATUS_DELETED = 9;

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
     * @var MetaCategory
     *
     * @ORM\ManyToOne(targetEntity="MetaCategory")
     * @ORM\JoinColumn(nullable=false)
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
     * @var Comment[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Comment",
     *      mappedBy="resource",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"publishedAt": "DESC"})
     */
    private $comments;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank
     */
    protected $status;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="File"
     * )
     * @ORM\JoinColumn(nullable=true)
     */
    protected $file;

    /**
     * Resource constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->editedAt = new \DateTime();

        $this->keywords = new ArrayCollection();

        $this->status = self::STATUS_DRAFT;
        $this->comments = new ArrayCollection();
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
    public function setCategory(MetaCategory $category): self
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
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File $file
     * @return Resource
     */
    public function setFile(File $file): Resource
    {
        $this->file = $file;
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        $comment->setResource($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        $this->comments->removeElement($comment);

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }
}
