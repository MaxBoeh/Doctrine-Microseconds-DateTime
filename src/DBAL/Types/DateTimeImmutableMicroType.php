<?php
declare(strict_types=1);

namespace OwlCorp\DoctrineMicrotime\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;

final class DateTimeImmutableMicroType extends BaseDateTimeMicroWithoutTz
{
    public const string NAME = 'datetime_immutable_micro';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format($this->getDateTimeFormatString($platform));
        }

        throw InvalidFormat::new(
            $value,
            $this->getName(),
            $this->getDateTimeFormatString($platform)
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        $phpVal = \DateTimeImmutable::createFromFormat($this->getDateTimeFormatString($platform), $value);
        if ($phpVal !== false) {
            return $phpVal;
        }

        try {
            return new \DateTimeImmutable($value); //it is usually able to guess
        } catch (\Throwable $t) {
            throw InvalidFormat::new(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }
    }
}
