<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResourceRepository")
 * @ORM\Table(name="rir_resource")
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="resource.blank_annotation")
     * @Assert\Length(min=10, minMessage="resource.too_short_annotation")
     */
    private $annotation;

    /**
     * @var ResourceKeyword[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\ResourceKeyword", cascade={"persist"})
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(max="40", maxMessage="resource.too_many_keywords")
     */
    private $keywords;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @var ResourceComment[]|ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="ResourceComment",
     *      mappedBy="resource",
     *      orphanRemoval=true,
     *      cascade={"persist"}
     * )
     * @ORM\OrderBy({"publishedAt": "DESC"})
     */
    private $comments;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $source;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank
     */
    private $status;

    /**
     * @var ResourcePurpose
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourcePurpose")
     * @ORM\JoinColumn(nullable=false)
     */
    private $purpose;

    /**
     * @var ResourceDocumentType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourceDocumentType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $documentType;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $theme;

    // TECH


    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $extension;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $size;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $editedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $language;

    /**
     * @var ResourceType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourceType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    // SEARCH

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $category;

    /**
     * @var ResourceAccessLevel
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ResourceAccessLevel")
     * @ORM\JoinColumn(nullable=false)
     */
    private $accessLevel;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $approvedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $expiredAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->editedAt = new \DateTime();
        $this->approvedAt = new \DateTime();
        $this->status = self::STATUS_DRAFT;

        $this->keywords = new ArrayCollection();
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
     * @return Resource
     */
    public function setId(int $id): Resource
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
     * @return Resource
     */
    public function setTitle(string $title): Resource
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
     * @return Resource
     */
    public function setAnnotation(string $annotation): Resource
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
     * @return Resource
     */
    public function addKeyword(ResourceKeyword ...$keywords): Resource
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
     * @return Resource
     */
    public function removeKeyword(ResourceKeyword $keyword): Resource
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
     * @return Resource
     */
    public function setAuthor(User $author): Resource
    {
        $this->author = $author;
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(ResourceComment $comment): Resource
    {
        $comment->setResource($this);
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }

        return $this;
    }

    public function removeComment(Comment $comment): Resource
    {
        $this->comments->removeElement($comment);

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
     * @return Resource
     */
    public function setSource(string $source): Resource
    {
        $this->source = $source;
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
     * @return Resource
     */
    public function setStatus(int $status): Resource
    {
        $this->status = $status;
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
     * @return Resource
     */
    public function setPurpose(ResourcePurpose $purpose): Resource
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
     * @return Resource
     */
    public function setDocumentType(ResourceDocumentType $documentType): Resource
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
     * @return Resource
     */
    public function setTheme(string $theme): Resource
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
     * @param string $extension
     * @return Resource
     */
    public function setExtension(string $extension): Resource
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
     * @return Resource
     */
    public function setSize(int $size): Resource
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
     * @return Resource
     */
    public function setCreatedAt(\DateTime $createdAt): Resource
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
     * @return Resource
     */
    public function setEditedAt(\DateTime $editedAt): Resource
    {
        $this->editedAt = $editedAt;
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
     * @return Resource
     */
    public function setLanguage(string $language): Resource
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return ResourceType
     */
    public function getType(): ?ResourceType
    {
        return $this->type;
    }

    /**
     * @param ResourceType $type
     * @return Resource
     */
    public function setType(ResourceType $type): Resource
    {
        $this->type = $type;
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
     * @return Resource
     */
    public function setCategory(string $category): Resource
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
     * @return Resource
     */
    public function setAccessLevel(ResourceAccessLevel $accessLevel): Resource
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
     * @return Resource
     */
    public function setApprovedAt(\DateTime $approvedAt): Resource
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
     * @return Resource
     */
    public function setExpiredAt(\DateTime $expiredAt): Resource
    {
        $this->expiredAt = $expiredAt;
        return $this;
    }


}
