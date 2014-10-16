# Doctrine

Northern Doctrine is a small library that makes it easy to add Doctrine configuration to plain PHP applications without having to rely on frameworks such as Symfony 2.

## Installation

Simply add it to your Composer.json:

    "northern/doctrine": "dev-dev-master"

## DoctrineConfig

To use the `DoctrineConfig` class simply instantiate it and pass the path to the location in the file system when the YAML configuration files are located:

````php
use Northern\Doctrine\DoctrineConfig;

$doctrine = new DoctrineConfig( "./config", 'dev' );
````

The `DoctrineConfig` class tries to load 2 configuation files from the specified location. The first configuration file is called `config.yml` and contains the basic configuration. The `config.yml` has the following sections:

````yaml
doctrine:
  database:
    driver:   pdo_mysql
    dbname:   dbname
    user:     username
    password: password
    charset:  utf8

  entity:
    paths:
      - "src/Acme/Entity"

  proxy:
    path: "cache/doctrine"
    namespace: Acme\Entites\Proxies

  isDevMode: true
````                                     
The `database` section should be pretty straight, simply supply the database details.

The `entity` section specifies the file system location of where your entity class files are located. Notice that `paths` is an array and you can add multiple locations here.

The `proxy` section specifies where the proxy classes Doctine generates will be stored. It's also required to specify the `namespace` of your proxies. Usually this is just the regulat namespace of your entites followed by `Proxies`.

The `isDevMode` parameter is `true` by default but for production applications can be set to `false` instead.

Besides needing the `config.yml` described above, it's also required to have an environment specific configurations. In the example above where we instantiate the `DoctrineConfig` class we also specify the enviroment which in this case is `dev`. Because of this, `DoctrineConfig` will after loading the `config.yml` try to load the `config_dev.yml` file. The environment specific configuration contains the settings for that specific enviroment. Usually this are just the database connection settings, such as:

````yaml
doctrine:
  database
    dbname:   mydb
    user:     myuser
    password: secret
````
Make sure your enviroment config exists.

After instantiating the `DoctrineConfig` it's easy to get access to the Entity Manager:

````php
$em = $doctrine->getEntityManager();
````

