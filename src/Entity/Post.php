<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min=5, max=70)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $body;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     */
    private $categorizedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @Assert\IsTrue(message="Body should be twice longer than title.")
     */
    public function isBodyTwiceLongerThanTitle(): bool
    {
        return mb_strlen($this->body) / mb_strlen($this->title) > 2;
    }

    public function getCategorizedBy(): ?Category
    {
        return $this->categorizedBy;
    }

    public function setCategorizedBy(?Category $categorizedBy): self
    {
        $this->categorizedBy = $categorizedBy;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setInitialCreatedAt()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
