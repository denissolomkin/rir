<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="rir_resource_media_type")
 */
class ResourceMediaType implements \JsonSerializable
{
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
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var ResourceExtension[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\ResourceExtension", mappedBy="mediaType")
     */
    private $extensions;

    public function __construct()
    {
        $this->extensions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return ResourceExtension[]
     */
    public function getExtensions(): Collection
    {
        return $this->extensions;
    }

    public function addExtension(ResourceExtension $extension): self
    {
        $extension->setMediaType($this);
        if (!$this->extensions->contains($extension)) {
            $this->extensions->add($extension);
        }

        return $this;
    }

    public function removeExtension(ResourceExtension $extension): self
    {
        $this->extensions->removeElement($extension);

        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): string
    {
        // This entity implements JsonSerializable (http://php.net/manual/en/class.jsonserializable.php)
        // so this method is used to customize its JSON representation when json_encode()
        // is called, for example in tags|json_encode (app/Resources/views/form/fields.html.twig)

        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
