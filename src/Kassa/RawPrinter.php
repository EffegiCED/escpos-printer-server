<?php
declare(strict_types=1);

namespace Darkterminal\EscposPrinterServer\Kassa;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Throwable;

/**
 * Stampa grezza per Kassa (fork EffegiCED): i byte ESC/POS arrivano già pronti da Kassa e vanno alla stampante così come sono,
 * senza il modello di scontrino di questo server. La stampante è `IP:porta` (stampante di rete, porta 9100 se manca) oppure il
 * nome di una stampante installata e condivisa in Windows (come WindowsPrintConnector di escpos-php).
 */
final class RawPrinter
{
    /** Secondi per aprire il collegamento con la stampante di rete. */
    public const CONNECT_TIMEOUT = 5;

    /** Secondi per scrivere: una stampante senza carta può smettere di prendere dati senza errore. */
    public const WRITE_TIMEOUT = 20;

    /**
     * Dove va la stampa.
     *
     * @return array{interface: string, host?: string, port?: int, name?: string}
     */
    public static function target(string $printer): array
    {
        $printer = trim($printer);
        if ($printer === '') {
            throw new \InvalidArgumentException('Manca la stampante (IP:porta o nome della stampante di Windows).');
        }
        if (preg_match('/^((?:\d{1,3}\.){3}\d{1,3}|localhost|[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+)(?::(\d{1,5}))?$/', $printer, $m) === 1) {
            $port = isset($m[2]) ? (int) $m[2] : 9100;
            if ($port < 1 || $port > 65535) {
                throw new \InvalidArgumentException("Porta non valida: {$port}.");
            }

            return ['interface' => 'ethernet', 'host' => $m[1], 'port' => $port];
        }

        return ['interface' => 'windows', 'name' => $printer];
    }

    /** Controlla che la stampante di rete risponda, senza stampare. Null se va bene (o se è una stampante di Windows). */
    public static function probe(string $printer): ?string
    {
        $target = self::target($printer);
        if ($target['interface'] !== 'ethernet') {
            return null;
        }
        $socket = @fsockopen($target['host'], $target['port'], $errno, $errstr, self::CONNECT_TIMEOUT);
        if ($socket === false) {
            return "La stampante {$target['host']}:{$target['port']} non risponde ({$errstr}).";
        }
        fclose($socket);

        return null;
    }

    /**
     * Manda i byte. `stage` dice dove si è fermata: `connect` = alla stampante non è arrivato niente, `write` = può essere uscita
     * in parte (Kassa la segna incerta e non la ristampa da sola).
     *
     * @return array{ok: bool, stage?: string, error?: string}
     */
    public static function send(string $printer, string $bytes): array
    {
        $target = self::target($printer);
        if ($target['interface'] === 'windows') {
            try {
                $connector = new WindowsPrintConnector($target['name']);
                $connector->write($bytes);
                $connector->finalize();

                return ['ok' => true];
            } catch (Throwable $e) {
                return ['ok' => false, 'stage' => 'connect', 'error' => "Stampante di Windows «{$target['name']}»: {$e->getMessage()}"];
            }
        }

        $socket = @fsockopen($target['host'], $target['port'], $errno, $errstr, self::CONNECT_TIMEOUT);
        if ($socket === false) {
            return ['ok' => false, 'stage' => 'connect', 'error' => "La stampante {$target['host']}:{$target['port']} non risponde ({$errstr})."];
        }
        stream_set_timeout($socket, self::WRITE_TIMEOUT);

        $written = 0;
        $length = strlen($bytes);
        while ($written < $length) {
            $n = @fwrite($socket, substr($bytes, $written, 8192));
            if ($n === false || $n === 0) {
                $timedOut = (bool) (stream_get_meta_data($socket)['timed_out'] ?? false);
                fclose($socket);

                return [
                    'ok' => false,
                    'stage' => $written > 0 || $timedOut ? 'write' : 'connect',
                    'error' => $timedOut ? 'La stampante ha smesso di prendere i dati (carta finita o coperchio aperto?).' : 'Collegamento con la stampante interrotto.',
                ];
            }
            $written += $n;
        }
        fflush($socket);
        fclose($socket);

        return ['ok' => true];
    }
}
