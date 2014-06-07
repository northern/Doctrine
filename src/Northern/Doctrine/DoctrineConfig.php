<?php 

namespace Northern\Doctrine;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

use Symfony\Component\Yaml;

use Northern\Common\Helper\ArrayHelper as Arr;

class DoctrineConfig {
	
	protected $cache;
	protected $reader;
	protected $driver;
	protected $config;
	protected $entityManager;
	protected $platform;
	
	public function getEntityManager()
	{
		return $this->entityManager;
	}
	
	public function __construct( $configDir, $environment = 'dev' )
	{
		if( ! @file_exists("{$configDir}/config.yml") )
		{
			throw new \Exception("File: {$configDir}/config.yml does not exist.");
		}

		if( ! @file_exists("{$configDir}/config_{$environment}.yml") )
		{
			throw new \Exception("File: {$configDir}/config_{$environment}.yml does not exist.");
		}

		try
		{
			$config    = Yaml\Yaml::parse("{$configDir}/config.yml");
			$configEnv = Yaml\Yaml::parse("{$configDir}/config_{$environment}.yml");
		}
		catch( Yaml\Exception\ParseException $e )
		{
			throw new \Exception( $e->getMessage() );
		}

		$this->config = Arr::merge( $config, $configEnv );

		$entityPaths      = Arr::get( $this->config, 'doctrine.entity.paths', array() );
		$proxiesPath      = Arr::get( $this->config, 'doctrine.proxy.path', '' );
		$proxiesNamespace = Arr::get( $this->config, 'doctrine.proxy.namespace', '' );
		$database         = Arr::get( $this->config, 'doctrine.database', array() );
		$isDevMode        = Arr::get( $this->config, 'doctrine.isDevMode', TRUE );
		
		$this->cache = new \Doctrine\Common\Cache\ArrayCache();
		
		$this->reader = new AnnotationReader();
		$this->driver = new \Doctrine\ORM\Mapping\Driver\AnnotationDriver( $this->reader, $entityPaths );
		
		$this->config = Setup::createAnnotationMetadataConfiguration( $entityPaths, $isDevMode );
		$this->config->setMetadataCacheImpl( $this->cache );
		$this->config->setQueryCacheImpl( $this->cache );
		$this->config->setMetadataDriverImpl( $this->driver );
		$this->config->setProxyDir( $proxiesPath );
		$this->config->setProxyNamespace( $proxiesNamespace );
		
		$this->config->setAutoGenerateProxyClasses( $isDevMode );
		
		$this->entityManager = EntityManager::create( $database, $this->config );
		
		$this->platform = $this->entityManager->getConnection()->getDatabasePlatform();
		$this->platform->registerDoctrineTypeMapping('enum', 'string');
	}
	
}
