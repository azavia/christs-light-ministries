<?php
namespace Azavia\RadioBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadUnprocessedTracksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('azavia_radio:load_unprocessed_tracks')
        ->setDescription('Load unprocessed tracks into the database.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $track_loader = $this->getContainer()->get('azavia_radio.track_loader');
        $track_loader->loadUnprocessedTracks();
    }
}
