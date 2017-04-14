<?php

namespace Tlapnet\Report\Bridges\Tracy\Panel;

use Nette\DI\Container;
use ReflectionClass;
use Tlapnet\Report\Bridges\Nette\DI\ReportExtension;
use Tlapnet\Report\Utils\Arrays;
use Tracy\IBarPanel;

final class ReportPanel implements IBarPanel
{

	/** @var Container */
	private $container;

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Renders HTML code for custom tab.
	 *
	 * @return string
	 */
	public function getTab()
	{
		ob_start();
		$reports = $this->container->findByTag(ReportExtension::TAG_INTROSPECTION);
		require __DIR__ . '/templates/tab.phtml';

		return ob_get_clean();
	}

	/**
	 * Renders HTML code for custom panel.
	 *
	 * @return string
	 */
	public function getPanel()
	{
		$output = [];

		$prop = (new ReflectionClass(Container::class))->getProperty('registry');
		$prop->setAccessible(TRUE);
		$registry = $prop->getValue($this->container);

		$subreports = $this->container->findByTag(ReportExtension::TAG_INTROSPECTION);
		foreach ($subreports as $service => $tag) {
			$output[] = $def = (object) [
				'id' => 'rp' . md5(serialize($tag)),
				'file' => $tag['file'],
				'report' => $tag['report'],
				'subreport' => $tag['subreport'],
				'datasource' => (object) [
					'type' => $this->container->getServiceType($tag['datasource']),
					'name' => Arrays::pop(explode('\\', $this->container->getServiceType($tag['datasource']))),
					'created' => FALSE,
					'service' => NULL,
				],
				'renderer' => (object) [
					'type' => $this->container->getServiceType($tag['renderer']),
					'name' => Arrays::pop(explode('\\', $this->container->getServiceType($tag['renderer']))),
					'created' => FALSE,
					'service' => NULL,
				],
				'tags' => $tag,
			];

			if ((isset($registry[$tag['datasource']]) && !$registry[$tag['datasource']] instanceof Container)) {
				$def->datasource->created = TRUE;
				$def->datasource->service = $registry[$tag['datasource']];
			}
			if ((isset($registry[$tag['renderer']]) && !$registry[$tag['renderer']] instanceof Container)) {
				$def->renderer->created = TRUE;
				$def->renderer->service = $registry[$tag['renderer']];
			}
		}

		ob_start();
		$items = $output;
		require __DIR__ . '/templates/panel.phtml';

		return ob_get_clean();
	}

}