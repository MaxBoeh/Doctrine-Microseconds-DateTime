<?php
declare(strict_types=1);

namespace OwlCorp\DoctrineMicrotime\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\Exception\NotSupported;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use OwlCorp\DoctrineMicrotime\DBAL\Platform\DateTimeFormatTrait;

abstract class BaseDateTimeMicroWithoutTz extends Type
{
    use DateTimeFormatTrait;

    public const string NAME = 'datetime_micro';

    public function getName(): string
    {
        return static::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if ($platform instanceof PostgreSQLPlatform) {
            return 'TIMESTAMP(6) WITHOUT TIME ZONE';
        }

        if ($platform instanceof MySQLPlatform) {
            return $platform->getDateTimeTypeDeclarationSQL($column) . '(6)';
        }

        if ($platform instanceof OraclePlatform) {
            return 'TIMESTAMP(6)';
        }

        if ($platform instanceof SQLitePlatform || $platform instanceof SQLServerPlatform) {
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
