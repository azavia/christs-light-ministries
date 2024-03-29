<?php

namespace Azavia\RadioBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Azavia\RadioBundle\Entity\Genre
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Genre
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $genre
     *
     * @ORM\Column(name="genre", type="string", length=255, unique="true")
     */
    private $genre;

    /**
     * @var ArrayCollection $tracks
     *
     * @ORM\ManyToMany(targetEntity="Track", mappedBy="genres")
     */
    private $tracks;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set genre
     *
     * @param string $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * Get genre
     *
     * @return string 
     */
    public function getGenre()
    {
        return $this->genre;
    }
    public function __construct()
    {
        $this->tracks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add genres
     *
     * @param Azavia\RadioBundle\Entity\Track $genres
     */
    public function addTrack(\Azavia\RadioBundle\Entity\Track $genres)
    {
        $this->genres[] = $genres;
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