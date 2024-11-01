<?php
declare(strict_types=1);

namespace OwlCorp\DoctrineMicrotime\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use OwlCorp\DoctrineMicrotime\DBAL\Platform\DateTimeFormatTrait;

final class TimeMicroType extends BaseTimeMicro
{
    use DateTimeFormatTrait;

    public const string NAME = 'time_micro';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
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
        if ($value === null || $value instanceof \DateTimeInterface) {
            return $value;
        }

        //The "!" forces Y-m-d to be set to beginning of unix epoch
        $phpVal = \DateTime::createFromFormat('!' . $this->getTimeFormatString($platform), $value);
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
