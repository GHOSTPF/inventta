<?php

if (! function_exists('brl')) {
    /** Formata um valor em reais: 1234.5 => "R$ 1.234,50". */
    function brl(float|int|string|null $value): string
    {
        return 'R$ '.number_format((float) $value, 2, ',', '.');
    }
}
