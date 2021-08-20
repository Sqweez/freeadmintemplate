<?php

function ucfirstRu(string $value): string {
    return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
}
