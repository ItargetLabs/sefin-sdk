<?php

declare(strict_types=1);

namespace SefinSdk\Support;

final class XmlNormalizer
{
    public static function ensureUtf8(string $xml): string
    {
        $xml = ltrim($xml);

        if ($xml === '') {
            return $xml;
        }

        // Remove BOM UTF-8 (se existir)
        if (str_starts_with($xml, "\xEF\xBB\xBF")) {
            $xml = substr($xml, 3);
        }

        // Garante que a string está em UTF-8; se não estiver, tenta converter.
        if (function_exists('mb_check_encoding') && !mb_check_encoding($xml, 'UTF-8')) {
            if (function_exists('mb_convert_encoding')) {
                $xml = mb_convert_encoding($xml, 'UTF-8');
            }
        }

        // Se tiver prólogo, força encoding UTF-8 nele; senão, adiciona.
        if (preg_match('/^<\\?xml\\s[^?]*\\?>/i', $xml, $m) === 1) {
            $prolog = $m[0];
            if (preg_match('/encoding\\s*=\\s*([\'"])([^\'"]+)\\1/i', $prolog) === 1) {
                $prologFixed = preg_replace(
                    '/encoding\\s*=\\s*([\'"])([^\'"]+)\\1/i',
                    'encoding="UTF-8"',
                    $prolog
                );
                if (is_string($prologFixed) && $prologFixed !== '') {
                    $xml = $prologFixed . substr($xml, strlen($prolog));
                }
            } else {
                $prologFixed = rtrim(substr($prolog, 0, -2)) . ' encoding="UTF-8"?>';
                $xml = $prologFixed . substr($xml, strlen($prolog));
            }
        } else {
            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" . $xml;
        }

        return $xml;
    }
}

