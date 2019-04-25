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
class Resource extends AbstractResource
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
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank
     */
    protected $status;

    /**
     * Resource constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->status = self::STATUS_DRAFT;
        $this->comments = new ArrayCollection();
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
