<?php

namespace Azavia\RadioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Azavia\RadioBundle\Entity\Composer
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Azavia\RadioBundle\Entity\ComposerRepository")
 */
class Composer extends Contributor
{
    /**
     * @var ArrayCollection $tracks
     *
     * @ORM\ManyToMany(targetEntity="Track", mappedBy="composers")
     */
    private $tracks;

    public function __construct()
    {
        $this->tracks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add tracks
     *
     * @param Azavia\RadioBundle\Entity\Track $tracks
     */
    public function addTrack(\Azavia\RadioBundle\Entity\Track $tracks)
    {
        $this->tracks[] = $tracks;
    }

    /**
     * Get tracks
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTracks()
    {
        return $this->tracks;
    }
}