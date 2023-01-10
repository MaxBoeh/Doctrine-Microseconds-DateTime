<?php
declare(strict_types=1);

namespace OwlCorp\DoctrineMicrotime\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;

final class DateTimeMicroType extends BaseDateTimeMicroWithoutTz
{
    const NAME = 'datetime_micro';

    public function convertToDatabaseValue($phpVal, AbstractPlatform $platform)
    {
        if ($phpVal === null) {
            return $phpVal;
        }

        if ($phpVal instanceof \DateTimeInterface) {
            return $phpVal->format($this->getDateTimeFormatString($platform));
        }

        throw ConversionException::conversionFailedFormat(
            $phpVal,
            $this->getName(),
            $this->getDateTimeFormatString($platform)
        );
    }

    public function convertToPHPValue($dbVal, AbstractPlatform $platform)
    {
        if ($dbVal === null || $dbVal instanceof \DateTimeInterface) {
            return $dbVal;
        }

        $phpVal = \DateTime::createFromFormat($this->getDateTimeFormatString($platform), $dbVal);
        if ($phpVal !== false) {
            return $phpVal;
        }

        try {
            return new \DateTime($dbVal); //it is usually able to guess
        } catch (\Throwable $t) {
            throw ConversionException::conversionFailedFormat(
                $dbVal,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }
    }
}
