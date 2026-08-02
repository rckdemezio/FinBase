<?php

declare(strict_types=1);

namespace Demezio\Finbase\Finance\Application\Exception;

/**
 * Indica que já existe uma categoria com o mesmo nome global.
 */
final class DuplicateCategoryNameException extends \DomainException
{
}
