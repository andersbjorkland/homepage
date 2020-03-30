<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PortfolioEntry", mappedBy="image")
     */
    private $portfolioEntries;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="images")
     */
    private $post;

    public function __construct()
    {
        $this->portfolioEntries = new ArrayCollection();
        $this->updated = new \DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return Collection|PortfolioEntry[]
     */
    public function getPortfolioEntries(): Collection
    {
        return $this->portfolioEntries;
    }

    public function addPortfolioEntry(PortfolioEntry $portfolioEntry): self
    {
        if (!$this->portfolioEntries->contains($portfolioEntry)) {
            $this->portfolioEntries[] = $portfolioEntry;
            $portfolioEntry->setImage($this);
        }

        return $this;
    }

    public function removePortfolioEntry(PortfolioEntry $portfolioEntry): self
    {
        if ($this->portfolioEntries->contains($portfolioEntry)) {
            $this->portfolioEntries->removeElement($portfolioEntry);
            // set the owning side to null (unless already changed)
            if ($portfolioEntry->getImage() === $this) {
                $portfolioEntry->setImage(null);
            }
        }

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
