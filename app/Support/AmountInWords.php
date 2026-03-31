<?php

namespace App\Support;

class AmountInWords
{
    public static function mkdDenars($amount): string
    {
        $value = (int) round((float) $amount);

        if ($value === 0) {
            return 'нула денари';
        }

        return self::numberToMacedonianWords($value) . ' денари';
    }

    private static function numberToMacedonianWords(int $number): string
    {
        $ones = [
            '',
            'еден',
            'две',
            'три',
            'четири',
            'пет',
            'шест',
            'седум',
            'осум',
            'девет',
            'десет',
            'единаесет',
            'дванаесет',
            'тринаесет',
            'четиринаесет',
            'петнаесет',
            'шеснаесет',
            'седумнаесет',
            'осумнаесет',
            'деветнаесет',
        ];

        $tens = [
            '',
            '',
            'дваесет',
            'триесет',
            'четириесет',
            'педесет',
            'шеесет',
            'седумдесет',
            'осумдесет',
            'деведесет',
        ];

        $hundreds = [
            '',
            'сто',
            'двеста',
            'триста',
            'четиристотини',
            'петстотини',
            'шестстотини',
            'седумстотини',
            'осумстотини',
            'деветстотини',
        ];

        if ($number < 20) {
            return $ones[$number];
        }

        if ($number < 100) {
            $tenPart = $tens[(int) ($number / 10)];
            $onePart = $number % 10;

            return $onePart ? $tenPart . ' и ' . $ones[$onePart] : $tenPart;
        }

        if ($number < 1000) {
            $hundredPart = $hundreds[(int) ($number / 100)];
            $remainder = $number % 100;

            return $remainder ? $hundredPart . ' ' . self::numberToMacedonianWords($remainder) : $hundredPart;
        }

        if ($number < 1000000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;

            $thousandPart = $thousands === 1
                ? 'илјада'
                : self::numberToMacedonianWords($thousands) . ' илјади';

            return $remainder ? $thousandPart . ' ' . self::numberToMacedonianWords($remainder) : $thousandPart;
        }

        $millions = (int) ($number / 1000000);
        $remainder = $number % 1000000;

        $millionPart = $millions === 1
            ? 'еден милион'
            : self::numberToMacedonianWords($millions) . ' милиони';

        return $remainder ? $millionPart . ' ' . self::numberToMacedonianWords($remainder) : $millionPart;
    }
}
