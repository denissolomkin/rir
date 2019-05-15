<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class MetaCatalog implements \JsonSerializable
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
     * @var MetaMedia
     *
     * @ORM\ManyToOne(targetEntity="MetaMedia", inversedBy="extensions", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=true)
     * @Groups("group3")
     */
    private $mediaType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
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
     * @return MetaExtension
     */
    public function setMediaType(MetaMedia $mediaType): MetaExtension
    {
        $this->mediaType = $mediaType;
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
