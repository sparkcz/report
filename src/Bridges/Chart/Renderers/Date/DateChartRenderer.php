<?php

namespace Tlapnet\Report\Bridges\Chart\Renderers\Date;

use DateTime;
use Tlapnet\Chart\DateChart;
use Tlapnet\Chart\Segment\DateSegment;
use Tlapnet\Chart\Serie\DateSerie;
use Tlapnet\Report\Bridges\Chart\Renderers\SeriesChartRenderer;
use Tlapnet\Report\Heap\Heap;

class DateChartRenderer extends SeriesChartRenderer
{

	/** @var bool */
	protected $useTimePrecision;

	/**
	 * @param boolean $use
	 */
	public function setUseTimePrecision($use = TRUE)
	{
		$this->useTimePrecision = $use;
	}

	/**
	 * @param object $serie
	 * @return DateSerie
	 */
	protected function createSerie($serie)
	{
		return new DateSerie($serie->type, $serie->title, $serie->color);
	}

	/**
	 * RENDERERING *************************************************************
	 */

	/**
	 * @param Heap $heap
	 * @return mixed
	 */
	public function render(Heap $heap)
	{
		/** @var DateChart $chart */
		$chart = $this->createChart(new DateChart());

		if ($this->useTimePrecision) {
			$this->setUseTimePrecision($this->useTimePrecision);
		}

		// Create series
		$series = $this->doPrepareSeries($chart, $this->getSeries());

		// Filter data
		$filtered = $this->doFilterData($this->getSeriesBys(), $heap->getData());

		$xKey = $this->getSegment('x');
		$yKey = $this->getSegment('y');

		foreach ($filtered as $serieName => $items) {
			/** @var DateSerie $serie */
			$serie = $series[$serieName];
			foreach ($items as $item) {
				$serie->addSegment(new DateSegment(new DateTime($item[$xKey]), $item[$yKey]));
			}
		}

		// Add series to chart
		$this->doAddSeries($chart, $series);

		return $chart;
	}

}
