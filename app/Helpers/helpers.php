<?php
// use NumberFormatter;

if (!function_exists('toArabicAmountInWords')) {
    function toArabicAmountInWords($amount)
    {
        $parts = explode('.', number_format($amount, 3, '.', ''));
        $dirhams = (int)$parts[0];
        $fils = isset($parts[1]) ? (int)substr($parts[1], 0, 3) : 0;

        $formatter = new NumberFormatter('ar', NumberFormatter::SPELLOUT);

        $words = $formatter->format($dirhams) . ' درهم';

        if ($fils > 0) {
            $words .= ' و ' . $formatter->format($fils) . ' فلس';
        }

        if ($fils == 0) {
            $words .= ' فقط';
        }

        return $words;
    }
}

if (!function_exists('toArabicNumerals')) {
    function toArabicNumerals($number) {
        $western = ['0','1','2','3','4','5','6','7','8','9'];
        $eastern = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        return str_replace($western, $eastern, $number);
    }
}

    if (!function_exists('DateFormatDateTime')) {
        function DateFormatDateTime($date)
        {
            return date('d-M-Y | h:i:s A', strtotime($date));
        }
    }