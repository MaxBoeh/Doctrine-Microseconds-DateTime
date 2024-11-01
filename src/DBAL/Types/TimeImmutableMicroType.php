<?php
declare(strict_types=1);

namespace OwlCorp\DoctrineMicrotime\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use OwlCorp\DoctrineMicrotime\DBAL\Platform\DateTimeFormatTrait;

final class TimeImmutableMicroType extends BaseTimeMicro
{
    use DateTimeFormatTrait;

    public const string NAME = 'time_immutable_micro';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof \DateTimeImmutable) {
            return $value->format($this->getTimeFormatString($platform));
        }

        throw InvalidFormat::new(
            $value,
            $this->getName(),
            $this->getTimeFormatString($platform)
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        //The "!" forces Y-m-d to be set to beginning of unix epoch
        $phpVal = \DateTimeImmutable::createFromFormat('!' . $this->getTimeFormatString($platform), $value);
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
