<?php

namespace Northern\Doctrine\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

class LoadFixturesCommand extends Command {

	protected function configure()
	{
		$this
			->setName('fixtures:load')
			->setDescription('Loads the fixtures.')
			->setDefinition(
				array(
					new InputArgument('directory', InputArgument::REQUIRED, 'The directory where the fixtures are located.'),
				)
			)
			->setHelp( <<<EOT
The <info>directory</info> specifies the directory where the fixtures are kept. The fixtures are defined as a set of files.
More information can be found here: <comment>https://github.com/doctrine/data-fixtures</comment>
EOT
			)
		;
	}

	protected function execute( InputInterface $input, OutputInterface $output )
	{
		if( ( $directory = $input->getArgument('directory') ) === NULL )
		{
			throw new \RuntimeException("Argument 'directory' is required in order to execute this command correctly.");
		}
        
		$entityManager = $this->getHelper('em')->getEntityManager();
		
		$loader = new Loader();
		$loader->loadFromDirectory( $directory );
		
		$purger = new ORMPurger( $entityManager );
		$executor = new ORMExecutor( $entityManager, $purger );
		
		$executor->execute( $loader->getFixtures() );
	}
	
}
