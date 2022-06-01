<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommentsRepository::class)
 */
class Comments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $message;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $likes;

    /**
     * @ORM\ManyToOne(targetEntity=Posts::class, inversedBy="comments")
     */
    private $posts;

    /**
     * @ORM\ManyToOne(targetEntity=Comments::class, inversedBy="subComments")
     */
    private $comment;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="comment")
     */
    private $subComments;

    public function __construct()
    {
        $this->subComments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getPosts(): ?Posts
    {
        return $this->posts;
    }

    public function setPosts(?Posts $posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    public function getComment(): ?self
    {
        return $this->comment;
    }

    public function setComment(?self $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSubComments(): Collection
    {
        return $this->subComments;
    }

    public function addSubComment(self $subComment): self
    {
        if (!$this->subComments->contains($subComment)) {
            $this->subComments[] = $subComment;
            $subComment->setComment($this);
        }

        return $this;
    }

    public function removeSubComment(self $subComment): self
    {
        if ($this->subComments->removeElement($subComment)) {
            // set the owning side to null (unless already changed)
            if ($subComment->getComment() === $this) {
                $subComment->setComment(null);
            }
        }

        return $this;
    }
}
