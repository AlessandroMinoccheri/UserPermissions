<?php
namespace UserPermissions\Exception;

use Cake\Core\Exception\Exception;

/**
 * An instance of this exception should be thrown, if the
 * UserPermissionsComponent instance tries to call an handler which does not
 * exist.
 */
class MissingHandlerException extends Exception
{};
