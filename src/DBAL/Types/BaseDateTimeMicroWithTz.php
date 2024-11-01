<?php
declare(strict_types=1);

namespace OwlCorp\DoctrineMicrotime\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\Exception\NotSupported;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServer2008Platform;
use Doctrine\DBAL\Types\Type;
use OwlCorp\DoctrineMicrotime\DBAL\Platform\DateTimeFormatTrait;

abstract class BaseDateTimeMicroWithTz extends Type
{
    use DateTimeFormatTrait;

    public const string NAME = 'datetimetz_micro';

    public function getName(): string
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if ($platform instanceof PostgreSQLPlatform || $platform instanceof OraclePlatform) {
            return 'TIMESTAMP(6) WITH TIME ZONE';
        }

        if ($platform instanceof SQLServer2008Platform) {
            return $platform->getDateTimeTypeDeclarationSQL($column);
        }

        throw new NotSupported(
            \sprintf(
                '%s ("%s") type is not supported on "%s" platform',
                $this->getName(),
                static::class,
                \get_class($platform)
            )
        );
    }
}
