<?php 

namespace Northern\Doctrine;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

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
	
	public function __construct( array $configurations, $environment = 'dev' )
	{
		$this->config = Arr::get( $configurations, 'default', array() );
		
		if( isset( $configurations[ $environment ] ) )
		{
			$this->config = Arr::merge( $this->config, $configurations[ $environment ] );
		}
		
		$entityPaths      = Arr::get( $this->config, 'entityPaths' );
		$proxiesPath      = Arr::get( $this->config, 'proxies.path' );
		$proxiesNamespace = Arr::get( $this->config, 'proxies.namespace' );
		$database         = Arr::get( $this->config, 'database' );
		$isDevMode        = Arr::get( $this->config, 'isDevMode', TRUE );
		
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
