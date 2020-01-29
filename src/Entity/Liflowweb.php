<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LiflowwebRepository")
 */
class Liflowweb
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $userid;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $cluster;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $homedir;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $targetdir;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $binarypath;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $templatename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slurmcommand;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jobid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    //public function setDate(\DateTimeInterface $date): self
    public function setDate(): self
    {
        //$this->date = $date;
	$this->date = new \DateTime();

        return $this;
    }

    public function getUserid(): ?string
    {
        return $this->userid;
    }

    public function setUserid(string $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getCluster(): ?string
    {
        return $this->cluster;
    }

    public function setCluster(string $cluster): self
    {
        $this->cluster = $cluster;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getHomedir(): ?string
    {
        return $this->homedir;
    }

    public function setHomedir(string $homedir): self
    {
        $this->homedir = $homedir;

        return $this;
    }

    public function getTargetdir(): ?string
    {
        return $this->targetdir;
    }

    public function setTargetdir(string $targetdir): self
    {
        $this->targetdir = $targetdir;

        return $this;
    }

    public function getBinarypath(): ?string
    {
        return $this->binarypath;
    }

    public function setBinarypath(string $binarypath): self
    {
        $this->binarypath = $binarypath;

        return $this;
    }

    public function getTemplatename(): ?string
    {
        return $this->templatename;
    }

    public function setTemplatename(?string $templatename): self
    {
        $this->templatename = $templatename;

        return $this;
    }

    public function getSlurmcommand(): ?string
    {
        return $this->slurmcommand;
    }

    public function setSlurmcommand(?string $slurmcommand): self
    {
        $this->slurmcommand = $slurmcommand;

        return $this;
    }

    public function getJobid(): ?int
    {
        return $this->jobid;
    }

    public function setJobid(?int $jobid): self
    {
        $this->jobid = $jobid;

        return $this;
    }
}
