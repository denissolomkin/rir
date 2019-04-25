<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var array
     *
     * @ORM\Column(type="simple_array", nullable=false)
     * @Assert\NotBlank
     */
    protected $statuses;

    /**
     * Resource constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
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


}
