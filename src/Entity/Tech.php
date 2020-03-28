<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TechRepository")
 */
class Tech
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PortfolioEntry", mappedBy="tech")
     */
    private $portfolioEntries;

    public function __construct()
    {
        $this->portfolioEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $portfolioEntry->setTech($this);
        }

        return $this;
    }

    public function removePortfolioEntry(PortfolioEntry $portfolioEntry): self
    {
        if ($this->portfolioEntries->contains($portfolioEntry)) {
            $this->portfolioEntries->removeElement($portfolioEntry);
            // set the owning side to null (unless already changed)
            if ($portfolioEntry->getTech() === $this) {
                $portfolioEntry->setTech(null);
            }
        }

        return $this;
    }
}
