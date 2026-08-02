<?php

declare(strict_types=1);

namespace Demezio\Finbase\Core\Http;

/**
 * Indica que o corpo da requisição não contém um objeto JSON válido.
 */
final class InvalidJsonException extends \RuntimeException
{
}
