<?php
/**
 * @package Nexcess-CLI
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types = 1);

namespace Nexcess\Sdk\Cli\Util;

use Exception,
  Throwable;

use at\exceptable\Handler as ExceptableHandler;

use Nexcess\Sdk\ {
  ApiException,
  SdkException
};

use Nexcess\Sdk\Cli\ {
  Console,
  ConsoleException
};

use Symfony\Component\Console\ {
  Input\InputInterface as Input,
  Output\OutputInterface as Output
};

/**
 * Error/exception handling for nexcess-cli.
 *
 * @todo Untangle exception rendering from symfony application
 */
class ErrorHandler extends ExceptableHandler {

  /** @var Console The nexcess-cli console we're handling errors from. */
  protected $_console;

  /** @var Input Console input object. */
  protected $_input;

  /** @var Output Console output object. */
  protected $_output;

  /** @var bool Prior state of $application::$catchExceptions. */
  protected $_prev_catch_exceptions = true;

  /**
   * @param Console $console The nexcess-cli console we're handling errors from
   * @param Input $input Console input object
   * @param Output $output Console output object
   */
  public function __construct(Console $console, Input $input, Output $output) {
    $this->_console = $console;
    $this->_input = $input;
    $this->_output = $output;
    $this
      ->throw(E_ALL)
      ->onException([$this, 'handleException']);
  }

  /**
   * Dispatcher for handling exceptions.
   *
   * @param Throwable $e The exception to handle
   */
  public function handleException(Throwable $e) {
    switch (get_class($e)) {
      case ApiException::class:
        $code = Console::EXIT_API_ERROR;
        break;
      case ConsoleException::class:
        $code = Console::EXIT_CONSOLE_ERROR;
        break;
      case SdkException::class:
        $code = Console::EXIT_SDK_ERROR;
        break;
      default:
        $code = Console::EXIT_CATCHALL;
        break;
    }

    // symfony typehints exception, not throwable  :/
    if (! $e instanceof Exception) {
      $e = new Exception($e->getMessage(), $e->getCode(), $e);
    }
    $this->_console->renderException($e, $this->_output);
    if ($this->_console->isDebug()) {
      if ($e instanceof ApiException) {
        ob_start();
        var_dump($this->_console->getLastRequest());
        $this->_console->say(ob_get_clean());
      }
    }

    exit($code);
  }

  /**
   * {@inheritDoc}
   * Disable symfony error handling when active.
   */
  public function register() : ExceptableHandler {
    $this->_prev_catch_exceptions = $this->_console->areExceptionsCaught();
    $this->_console->setCatchExceptions(false);

    return parent::register();
  }

  /**
   * {@inheritDoc}
   * Re-establish symfony error handling when disabled.
   */
  public function unregister() : ExceptableHandler {
    $this->_console->setCatchExceptions($this->_prev_catch_exceptions);

    return parent::unregister();
  }
}
