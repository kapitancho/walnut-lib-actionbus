<?php
use PHPUnit\Framework\TestCase;
use Walnut\Lib\ActionBus\Action;
use Walnut\Lib\ActionBus\Handler\Mapper\NameActionHandlerMapper;

final class NameActionHandlerMapperAction implements Action {}
final class NameActionHandlerMapperActionHandlerTestSuffix {}

final class NameActionHandlerMapperTest extends TestCase {
	private function getActionHandlerMapper(): NameActionHandlerMapper {
		return new NameActionHandlerMapper('HandlerTestSuffix');
	}

	public function testOk(): void {
		$this->assertEquals(
			NameActionHandlerMapperActionHandlerTestSuffix::class,
			$this->getActionHandlerMapper()->getActionHandler(
				new NameActionHandlerMapperAction
			)
		);
	}

}