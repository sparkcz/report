<?php

namespace Tlapnet\Report\Bridges\Dibi\DataSources;

use Nette\Database\Connection;
use Tlapnet\Report\DataSource\AbstractDatabaseDataSource;
use Tlapnet\Report\Heap\Heap;
use Tlapnet\Report\HeapBox\ParameterList;

class NetteDatabaseDataSource extends AbstractDatabaseDataSource
{

    /** @var Connection */
    protected $connection;

    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->connection = new Connection(
            $this->config['dns'],
            $this->config['user'],
            $this->config['password']
        );
    }


    /**
     * @param ParameterList $parameters
     * @return Heap
     */
    public function compile(ParameterList $parameters)
    {
        $expander = $parameters->createExpander();
        $sql = $this->getSql();

        // Replace placeholders
        $query = $expander->expand($sql);

        // Execute query
        $resulset = $this->connection->query($query);

        $heap = new Heap($resulset->fetchAll());

        return $heap;
    }

}
