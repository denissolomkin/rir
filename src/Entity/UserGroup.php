<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class UserGroup implements \Serializable, \JsonSerializable
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
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;


    /**
     * @var MetaDocumentType[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MetaDocumentType", cascade={"persist"})
     */
    protected $documentTypes;


    public function __construct()
    {
        $this->documentTypes =  new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return UserAccess
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return UserAccess
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return MetaDocumentType[]|ArrayCollection
     */
    public function getDocumentTypes(): ArrayCollection
    {
        return $this->documentTypes;
    }

    /**
     * @param MetaDocumentType[]|ArrayCollection $documentTypes
     * @return UserGroup
     */
    public function setDocumentTypes($documentTypes): self
    {
        $this->documentTypes = $documentTypes;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        return serialize([$this->id, $this->name]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->name] = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        // This entity implements JsonSerializable (http://php.net/manual/en/class.jsonserializable.php)
        // so this method is used to customize its JSON representation when json_encode()
        // is called, for example in tags|json_encode (app/Resources/views/form/fields.html.twig)

        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
