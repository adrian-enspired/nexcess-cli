<?php
/**
 * @package Nexcess-CLI
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types = 1);

namespace Nexcess\Sdk\Cli\Command\Tests;

use Throwable;

use Nexcess\Sdk\ {
  Client,
  Sandbox\ResourceHandler
};

use Nexcess\Sdk\Cli\ {
  Console,
  Command\Command,
  Tests\TestCase
};

use PhpUnit\Framework\ {
  ExpectationFailedException as PhpUnitException
};

use Symfony\Component\Console\ {
  Helper\QuestionHelper,
  Input\InputInterface as Input,
  Output\OutputInterface as Output,
  Question\Question,
  Tester\CommandTester
};

/**
 * Base class for nexcess-cli command testcases.
 */
abstract class CommandTestCase extends TestCase {

  /**
   * @group integration
   * @dataProvider runProvider
   *
   * @param array $invocation Map of args/opts to invoke the command with
   * @param array $interactions List of [expected, response]s for interactions
   * @param array|Throwable $expected Expected [exit_code, [output]] or exception
   */
  public function testRun(array $invocation, array $interactions, $expected) {
    if ($expected instanceof Throwable) {
      $this->setExpectedException($expected);
    }

    $actual = $this->_testRun(
      $this->_getSubject(),
      $invocation,
      $interactions
    );

    $this->assertEquals(
      $expected['exit_code'] ?? Console::EXIT_SUCCESS,
      $actual->getStatusCode()
    );

    $output = $actual->getDisplay();
    foreach ($expected['output'] ?? [] as $content) {
      $this->assertContains($content, $output);
    }
  }

  /**
   * @return array[] List of testcases
   */
  abstract public function runProvider() : array;

  /**
   * Gets a Console applciation instance for testing.
   *
   * @param array $options Config option overrides
   * @return Console A sandboxed console instance
   */
  protected function _getConsole(array $options = []) : Console {
    $options['sandboxed'] = true;
    $console = new Console($options);
    $console->getSandbox()->setRequestHandler([
      new ResourceHandler(Console::DIR, Client::DIR),
      'handle'
    ]);
    return $console;
  }

  /**
   * {@inheritDoc}
   * Provides a sandboxed Console for the subject, if none provided.
   */
  protected function _getSubject(...$constructor_args) {
    if (empty($constructor_args)) {
      $constructor_args = [$this->_getConsole()];
    }

    return parent::_getSubject(...$constructor_args);
  }

  /**
   * Executes a command and returns the CommandTester for making assertions.
   *
   * @param Command $command The command to execute (use Console::find()!)
   * @param array $invocation Map of args/opts to invoke the command with
   * @param array $interactions List of [expected, response]s for interactions
   * @return CommandTester The tester instance
   */
  protected function _testRun(
    Command $command,
    array $invocation,
    array $interactions = []
  ) : CommandTester {
    $tester = new CommandTester($command);

    if (! empty($interactions)) {
      $this->_mockInteractions($command, $interactions);
    }

    $invocation['command'] = $command->getName();
    $tester->execute($invocation);

    return $tester;
  }
}
