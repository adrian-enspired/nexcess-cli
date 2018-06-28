<?php
/**
 * @package Nexcess-CLI
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types = 1);

namespace Nexcess\Sdk\Cli\Command\CloudAccount\Tests;

use Nexcess\Sdk\Cli\ {
  Command\CloudAccount\Show,
  Command\Tests\ShowCommandTestCase
};

/**
 * Tests for cloud-account:show.
 */
class ShowTest extends ShowCommandTestCase {

  /** {@inheritDoc} */
  const _SUBJECT_FQCN = Show::class;

  /**
   * {@inheritDoc}
   */
  public function inputProvider() : array {}

  /**
   * {@inheritDoc}
   */
  public function lookupProvider() : array {}

  /**
   * {@inheritDoc}
   */
  public function runProvider() : array {}
}
