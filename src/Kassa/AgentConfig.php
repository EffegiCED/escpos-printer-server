<?php
declare(strict_types=1);

namespace Darkterminal\EscposPrinterServer\Kassa;

/**
 * Chi può stampare con l'agente: solo i siti elencati in `config/agent.json` (`allowed_origins`, per esempio
 * `["https://kassa.exxstore.it"]`) o nella variabile d'ambiente `KASSA_ALLOWED_ORIGINS` (separati da virgola). Il browser manda
 * l'origine della pagina e un altro sito non può fingerla: senza questo controllo qualunque pagina aperta sul computer potrebbe
 * stampare. Il file si rilegge a ogni richiesta, quindi una modifica vale senza riavviare il servizio.
 */
final class AgentConfig
{
    public const VERSION = '2.0.4-kassa.1';

    /** Il protocollo che Kassa si aspetta: cambia solo con un cambio incompatibile dei messaggi. */
    public const PROTOCOL = 'kassa-raw/1';

    public static function file(): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'agent.json';
    }

    /** @return list<string> */
    public static function allowedOrigins(): array
    {
        $origins = [];
        $env = getenv('KASSA_ALLOWED_ORIGINS');
        if (is_string($env) && trim($env) !== '') {
            $origins = explode(',', $env);
        }

        $file = self::file();
        if (is_file($file)) {
            // Il file lo scrive l'installazione con PowerShell, che può aggiungere il BOM
            $raw = preg_replace('/^\xEF\xBB\xBF/', '', (string) file_get_contents($file));
            $data = json_decode((string) $raw, true);
            $list = is_array($data) ? ($data['allowed_origins'] ?? []) : [];
            $origins = array_merge($origins, is_array($list) ? $list : [$list]);
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($origin): string => rtrim(strtolower(trim((string) $origin)), '/'),
            $origins,
        ))));
    }

    public static function allows(?string $origin): bool
    {
        $origin = rtrim(strtolower(trim((string) $origin)), '/');

        return $origin !== '' && in_array($origin, self::allowedOrigins(), true);
    }
}
