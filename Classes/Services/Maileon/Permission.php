<?php

namespace XQueue\Typo3MaileonIntegration\Services\Maileon;

class Permission
{
    public const NONE = 'none';
    public const SOI = 'soi';
    public const COI = 'coi';
    public const DOI = 'doi';
    public const DOI_PLUS = 'doi+';
    public const OTHER = 'other';

    private const CODE_MAP = [
        1 => self::NONE,
        2 => self::SOI,
        3 => self::COI,
        4 => self::DOI,
        5 => self::DOI_PLUS,
        6 => self::OTHER,
    ];

    public static function getPermission(int|string $codeOrString): string
    {
        if (is_numeric($codeOrString)) {
            return self::CODE_MAP[(int)$codeOrString] ?? self::OTHER;
        }

        $normalized = strtolower(trim($codeOrString));

        return in_array($normalized, self::CODE_MAP, true) ? $normalized : self::OTHER;
    }

    public static function getCode(string $permission): int
    {
        $code = array_search($permission, self::CODE_MAP, true);

        return $code !== false ? $code : 6;
    }
}
