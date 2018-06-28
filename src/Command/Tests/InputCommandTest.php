<?php
/**
 * @package Nexcess-CLI
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types = 1);

namespace Nexcess\Sdk\Cli\Command\Tests;

use Throwable;

use Nexcess\Sdk\Cli\Command\Tests\CommandTestCase;

use Symfony\Component\Console\ {
  Input\ArrayInput,
  Output\BufferedOutput
};

/**
 * Tests for InputCommand methods.
 */
abstract class InputCommandTestCase extends CommandTestCase {

  /**
   * @covers InputCommand::initialize
   * @covers InputCommand::getInput
   * @dataProvider inputProvider
   *
   * @param string[] $input Map of input args/opts for testcase
   * @param string|null $get Name of input to get
   * @param mixed|Throwable $expected Expected value or exception
   */
  public function testGetInput(array $input, ?string $get, $expected) {
    if ($expected instanceof Throwable) {
      $this->setExpectedException($expected);
    }

    $command = $this->_getSubject();
    $command->initialize(new ArrayInput($input), new BufferedOutput());

    $this->assertEquals($expected, $command->getInput($get));
  }

  /**
   * @return array[] List of testcases
   */
  abstract public function inputProvider() : array;

  /**
   * @covers InputCommand::lookupChoice
   * @dataProvider lookupProvider
   *
   * @param string $name Input name to lookup
   * @param string $lookup Value to lookup
   * @param mixed|Throwable $expected Expected value or throwable
   */
  public function testLookupChoice(string $name, string $lookup, $expected) {
    if ($expected instanceof Throwable) {
      $this->setExpectedException($expected);
    }

    $this->assertEquals(
      $expected,
      $this->_getSubject()->lookupChoice($name, $lookup)
    );
  }

  /**
   * @return array[] List of testcases
   */
  abstract public function lookupProvider() : array;
}
