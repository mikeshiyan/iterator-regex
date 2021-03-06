<?php

namespace Shiyan\Iterate\tests\unit;

use PHPUnit\Framework\TestCase;
use Shiyan\Iterate\Exception\BreakIteration;
use Shiyan\Iterate\Exception\ContinueIteration;
use Shiyan\Iterate\Iterate;
use Shiyan\Iterate\Scenario\BaseRegexScenario;
use Shiyan\Iterate\Scenario\BaseScenario;

class IterateTest extends TestCase {

  public function testInvoke() {
    $iterate = new Iterate();

    // Empty iterator.
    $scenario = $this->createMock(BaseRegexScenario::class);
    $scenario->expects($this->once())->method('preRun');
    $scenario->expects($this->once())->method('postRun');
    $scenario->expects($this->never())->method('preSearch');

    $iterate(new \ArrayIterator([]), $scenario);

    // Set current to other than first, but expect search in all of them.
    $iterator = new \ArrayIterator(['a', 'b', 'c']);
    $iterator->seek(1);

    $scenario = $this->createMock(BaseRegexScenario::class);
    $scenario->expects($this->once())->method('preRun');
    $scenario->expects($this->once())->method('postRun');
    $scenario->expects($this->exactly(3))->method('preSearch');
    $scenario->expects($this->exactly(3))->method('postSearch');

    $iterate($iterator, $scenario);

    // Scenario throws Continue.
    $scenario = $this->createMock(BaseScenario::class);
    $scenario->method('onEach')
      ->willThrowException(new ContinueIteration());
    $scenario->expects($this->exactly(3))->method('preSearch');
    $scenario->expects($this->never())->method('postSearch');

    $iterate($iterator, $scenario);

    // Scenario throws Break.
    $scenario = $this->createMock(BaseScenario::class);
    $scenario->method('onEach')
      ->willThrowException(new BreakIteration());
    $scenario->expects($this->exactly(1))->method('preSearch');
    $scenario->expects($this->never())->method('postSearch');

    $iterate($iterator, $scenario);
  }

}
