<?php
declare(strict_types=1);

namespace OwlCorp\DoctrineMicrotime\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;

final class DateTimeMicroType extends BaseDateTimeMicroWithoutTz
{
    public const string NAME = 'datetime_micro';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
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
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $phpVal = \DateTime::createFromFormat($this->getDateTimeFormatString($platform), $value);
        if ($phpVal !== false) {
            return $phpVal;
        }

        try {
            return new \DateTime($value); //it is usually able to guess
        } catch (\Throwable $t) {
            throw InvalidFormat::new(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }
    }
}
