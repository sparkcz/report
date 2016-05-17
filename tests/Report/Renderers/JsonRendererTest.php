<?php

namespace Tlapnet\Report\Tests\Renderers;

use Tlapnet\Report\Model\Data\Result;
use Tlapnet\Report\Renderers\JsonRenderer;
use Tlapnet\Report\Tests\BaseTestCase;

final class JsonRendererTest extends BaseTestCase
{

	public function testDefault()
	{
		$r = new JsonRenderer();
		$this->assertEquals('[{"foo":"bar"}]', $r->render(new Result([['foo' => 'bar']])));
		$this->assertEquals('[{"foo":"bar","foobar":"1"}]', $r->render(new Result([['foo' => 'bar', 'foobar' => 1]])));
	}

}