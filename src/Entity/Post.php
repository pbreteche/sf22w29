<?php

namespace App\Entity;

use App\Repository\PostRepository;
use App\Validator\WellFormedTitle;
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
     * @Assert\Length(min=5, max=70, groups="published")
     * @WellFormedTitle(groups={"draft", "published"})
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

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $writtenBy;

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

    public function getCategorizedBy(): ?Category
    {
        return $this->categorizedBy;
    }

    public function setCategorizedBy(?Category $categorizedBy): self
    {
        $this->categorizedBy = $categorizedBy;

        return $this;
    }

    public function getWrittenBy(): ?User
    {
        return $this->writtenBy;
    }

    public function setWrittenBy(?User $writtenBy): self
    {
        $this->writtenBy = $writtenBy;

        return $this;
    }

    /**
     * @Assert\IsTrue(message="Body should be twice longer than title.")
     */
    public function isBodyTwiceLongerThanTitle(): bool
    {
        return mb_strlen($this->body) / mb_strlen($this->title) > 2;
    }

    /**
     * @ORM\PrePersist
     */
    public function setInitialCreatedAt()
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
