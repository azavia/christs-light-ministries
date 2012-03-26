<?php

namespace Azavia\RadioBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr as Expr;

/**
 * TrackRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TrackRepository extends EntityRepository
{

    // {{{ scheduleTrack()

    public function scheduleTrack()
    {
        $em = $this->getEntityManager();

        $qb = $em->createQueryBuilder();

        $tracks = $this->findAll();

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
            ->from('AzaviaRadioBundle:Performance', 'p')
            ->where(
                    $qb->expr()->gte(
                        'p.played_at',
                        ':performance_interval'))
            ->andWhere(
                    $qb->expr()->lte(
                        'p.played_at',
                        'CURRENT_TIMESTAMP()'));

        $qb->setParameter(
                'performance_interval',
                new \DateTime('-3 hours'),
                Type::DATETIME);

        $performances = $qb->getQuery()->getResult();

        // {{{ Filter results

        $albums = array();
        $artists = array();
        $excludedTracks = array();
        $excludedAlbums = array();
        $excludedArtists = array();

        $albumMargin = new \DateTime('-90 minutes');
        $artistMargin = new \DateTime('-1 hour');

        foreach ($performances as $performance)
        {
            $excludedTracks[] = $performance->getTrack()->getId();

            if ($performance->getPlayedAt() > $albumMargin) {
                $excludedAlbums[] = $performance->getTrack()
                    ->getAlbum()->getName();
            }

            if ($performance->getPlayedAt() > $artistMargin) {
                foreach ($performance->getTrack()->getArtists() as $atist)
                {
                    $excludedArtists[] = $artist->getName();
                }
            }

            foreach ($performance->getTrack()->getArtists() as $artist)
            {
                if (isset($artists[$artist->getName()])) {
                    $artists[$artist->getName()]++;
                }
                else {
                    $artists[$artist->getName()] = 1;
                }
            }

            if ($performance->getTrack()->getAlbum())
            {
                if (isset($albums[$performance->getTrack()->getAlbum()->getName()])) {
                    $albums[$performance->getTrack()->getAlbum()->getName()]++;
                }
                else {
                    $albums[$performance->getTrack()->getAlbum()->getName()] = 1;
                }
            }
        }

        foreach ($artists as $artist => $number)
        {
            if ($number >= 3) {
                $excludedArtists[] = $artist;
            }
        }

        foreach ($albums as $album => $number)
        {
            if ($number >= 2) {
                $excludedAlbums[] = $album;
            }
        }

        $filteredTracks = array();

        foreach ($tracks as $track)
        {
            if (in_array($track->getId(), $excludedTracks)) {
                continue;
            }
            if ($track->getAlbum()) {
                if (in_array(
                            $track->getAlbum()->getName(),
                            $excludedAlbums)) {
                    continue;
                }
            }

            foreach ($track->getArtists() as $artist)
            {
                if (in_array($artist->getName(), $excludedArtists)) {
                    continue 2;
                }
            }

            $filteredTracks[] = $track;
        }

        // }}}

        return $filteredTracks[array_rand($filteredTracks)];
    }

    // }}}

}
