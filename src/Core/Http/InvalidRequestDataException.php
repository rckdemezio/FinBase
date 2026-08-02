<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

/**
 * Indica que os dados recebidos não atendem ao formato esperado pelo endpoint.
 */
final class InvalidRequestDataException extends \RuntimeException
{
}
