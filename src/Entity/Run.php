<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"running:read"}, "swagger_definition_name"="Read"},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\RunRepository")
 */
class Run
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"running:read"})
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $weightPre;

    /**
     * @ORM\Column(type="float")
     */
    private $weightPost;

    /**
     * @ORM\Column(type="float")
     * @Groups({"running:read"})
     */
    private $weightDiff;

    /**
     * @ORM\Column(type="float")
     * @Groups({"running:read"})
     */
    private $distance;

    /**
     * @ORM\Column(type="time")
     * @Groups({"running:read"})
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RunType", inversedBy="runs")
     * @Groups({"running:read"})
     */
    private $type;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getWeightPre(): ?float
    {
        return $this->weightPre;
    }

    public function setWeightPre(float $weightPre): self
    {
        $this->weightPre = $weightPre;

        return $this;
    }

    public function getWeightPost(): ?float
    {
        return $this->weightPost;
    }

    public function setWeightPost(float $weightPost): self
    {
        $this->weightPost = $weightPost;

        return $this;
    }

    public function getWeightDiff(): ?float
    {
        return $this->weightDiff;
    }

    public function setWeightDiff(float $weightDiff): self
    {
        $this->weightDiff = $weightDiff;

        return $this;
    }

    public function getDistance(): ?float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getType(): ?RunType
    {
        return $this->type;
    }

    public function setType(?RunType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
