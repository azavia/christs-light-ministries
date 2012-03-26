<?php
/**
 * Track Loader for loading unprocessed track files.
 *
 * @author Brandon Olivares <programmer2188@gmail.com>
 * @copyright Copyright © 2012 Brandon Olivares.
 * @version 0.1
 */

namespace Azavia\RadioBundle;

use Azavia\RadioBundle\Entity\Album;
use Azavia\RadioBundle\Entity\Artist;
use Azavia\RadioBundle\Entity\Composer;
use Azavia\RadioBundle\Entity\Genre;
use Azavia\RadioBundle\Entity\Track;
use Symfony\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Finder\Finder;

class TrackLoader
{

    protected $doctrine;
    protected $processed_track_dir;
    protected $unprocessed_track_dir;
    
    public function __construct(
            Registry $doctrine,
            $processed_track_dir,
            $unprocessed_track_dir)
    {
        $this->doctrine = $doctrine;
        $this->processed_track_dir = $processed_track_dir;
        $this->unprocessed_track_dir = $unprocessed_track_dir;
        $this->checkPaths();
    }
    
    /**
     * Load unprocessed tracks into the database.
     *
     * This method will process all tracks in the unprocessed track
     * directory, move them to the processed track directory, and enter its
     * metadata into the database.
     * 
     * @access public
     * @return void
     */
    public function loadUnprocessedTracks()
    {
        $iterator = Finder::create()
        ->files()
        ->name('*.mp3')
        ->depth(0)
        ->in($this->unprocessed_track_dir)
        ->getIterator();
        
        $em = $this->doctrine->getEntityManager();
        
        $id3 = new \GetId3_GetId3;
        
        // For each unprocessed track, read its metadata, save the track
        // with its metadata to the database, and move the track to the
        // processed tracks directory.
        foreach ($iterator as $file)
        {
            $tags = $id3->analyze($file->getRealPath());
            \GetId3_GetId3Lib::CopyTagsToComments($tags);
            
            $data = $this->formatId3Data($tags);
            
            $track = new Track;
            $track->setUnprocessedTrackDir($this->unprocessed_track_dir);
            $track->setProcessedTrackDir($this->processed_track_dir);
            
            $track->setFilename($data['filename']);
            $track->setLength($data['length']);
            
            if (isset($data['title'])) {
                $track->setTitle($data['title']);
            }
            
            if (isset($data['album'])) {
                $album = $this->doctrine->
                    getRepository(
                            'AzaviaRadioBundle:Album'
                            )->findOneByName($data['album']);
                
                if (!$album) {
                    $album = new Album;
                    $album->setName($data['album']);
                    $em->persist($album);
                }
                
                $track->setAlbum($album);
            }
            
            if (isset($data['artists'])) {
                $artists = $this->doctrine->
                    getRepository(
                            'AzaviaRadioBundle:Artist'
                            )->parse($data['artists']);
                
                foreach ($artists as $artist)
                {
                    $track->addArtist($artist);
                }
            }
            
            if (isset($data['composers'])) {
                $composers = $this->doctrine->
                    getRepository(
                            'AzaviaRadioBundle:Composer'
                            )->parse($data['composers']);
                
                foreach ($composers as $composer)
                {
                    $track->addComposer($composer);
                }
            }
            
            if (isset($data['year'])) {
                $track->setYear(intval($data['year']));
            }
            
            if (isset($data['genres'])) {
                foreach ($data['genres'] as $genre)
                {
                    $genreObject = $this->doctrine
                        ->getRepository(
                                'AzaviaRadioBundle:Genre'
                                )->findOneByGenre($genre);

                    if (!$genreObject) {
                        $genreObject = new Genre;
                        $genreObject->setGenre($genre);
                        $em->persist($genreObject);
                    }
                }
            }
            
            $em->persist($track);
            $em->flush();
        }
        
    }
    
    protected function checkPaths()
    {
        if (!file_exists($this->processed_track_dir))
        {
            mkdir($this->processed_track_dir, 0777, true);
        }

        if (!file_exists($this->unprocessed_track_dir))
        {
            mkdir($this->unprocessed_track_dir, 0777, true);
        }
    }
    
    protected function formatId3Data($tags)
    {
        $data = array();
        $data['filename'] = $tags['filename'];
        $data['length'] = $tags['playtime_seconds'];
        
        if (isset($tags['comments']['title']))
        {
            $data['title'] = $tags['comments']['title'][0];
        }
        
        if (isset($tags['comments']['album']))
        {
            $data['album'] = $tags['comments']['album'][0];
        }
        
        if (isset($tags['comments']['artist']))
        {
            $data['artists'] = $tags['comments']['artist'];
        }
        
        if (isset($tags['comments']['composer']))
        {
            $data['composers'] = $tags['comments']['composer'];
        }
        
        if (isset($tags['comments']['year']))
        {
            $data['year'] = intval($tags['comments']['year'][0]);
        }
        
        if (isset($tags['comments']['genre']))
        {
            $data['genres'] = $tags['comments']['genre'];
        }
            
        return $data;
    }

}
