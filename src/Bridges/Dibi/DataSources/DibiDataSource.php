<?php

namespace Tlapnet\Report\Bridges\Dibi\DataSources;

use DibiConnection;
use DibiException;
use Tlapnet\Report\DataSources\AbstractDatabaseConnectionDataSource;
use Tlapnet\Report\Exceptions\Runtime\DataSource\SqlException;
use Tlapnet\Report\Model\Parameters\Parameters;
use Tlapnet\Report\Model\Result\Result;

class DibiDataSource extends AbstractDatabaseConnectionDataSource
{

	/** @var DibiConnection */
	protected $connection;

	/**
	 * @return void
	 */
	protected function connect()
	{
		$this->connection = new DibiConnection([
			'driver' => $this->getConfig('driver'),
			'host' => $this->getConfig('host'),
			'username' => $this->getConfig('user'),
			'password' => $this->getConfig('password'),
		]);
	}

	/**
	 * COMPILING ***************************************************************
	 */

	/**
	 * @param Parameters $parameters
	 * @return Result
	 * @throws SqlException
	 */
	public function compile(Parameters $parameters)
	{
		// Connect to DB
		if (!$this->connection) $this->connect();

		// Get SQL
		$sql = $this->getRealSql($parameters);

		try {
			// Prepare parameters
			if (!$parameters->isEmpty() || $parameters->hasDefaults()) {
				$switch = $parameters->createSwitcher();
				$switch->setPlaceholder('?');
				// Replace named parameters for ? and return
				// accurate sequenced array of arguments
				list ($sql, $args) = $switch->execute($sql);
			} else {
				// Keep empty arguments
				$args = [];
			}

			// Execute dibi query
			$query = array_merge([$sql], $args);
			$resultset = $this->connection->query($query);
		} catch (DibiException $e) {
			throw new SqlException($sql, NULL, $e);
		}

		$result = new LazyDibiResult($resultset);

		return $result;
	}

}
