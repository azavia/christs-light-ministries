<?php
namespace Azavia\RadioBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScheduleTrackCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('azavia_radio:schedule_track')
        ->setDescription('Schedule a track to play in the playlist.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $trackScheduler = $this->getContainer()
            ->get('azavia_radio.track_scheduler');
        $track = $trackScheduler->scheduleTrack();
$output->writeln(
        $trackScheduler->getProcessedTrackDir() . '/' . $track->getId() .
        '.mp3');

$metadataUpdater = $this->getContainer()
    ->get('azavia_radio.metadata_updater');
$metadataUpdater->updateMetadata($track);

$twitterStatusUpdater = $this->getContainer()
    ->get('azavia_radio.twitter_status_updater');
$twitterStatusUpdater->updateStatus($track);
    }
}
