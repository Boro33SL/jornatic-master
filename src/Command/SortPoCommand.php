<?php
declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Exception;

/**
 * Comando para ordenar alfabéticamente los archivos .po
 */
class SortPoCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        $parser->setDescription('Ordena alfabéticamente las entradas de archivos .po por msgid');

        $parser->addOption('file', [
            'short' => 'f',
            'help' => 'Archivo .po específico a ordenar',
            'default' => 'resources/locales/es/default.po',
        ]);

        $parser->addOption('backup', [
            'short' => 'b',
            'help' => 'Crear backup antes de ordenar',
            'boolean' => true,
            'default' => true,
        ]);

        return $parser;
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $file = $args->getOption('file');
        $createBackup = $args->getOption('backup');

        $filePath = ROOT . DS . $file;

        if (!file_exists($filePath)) {
            $io->error("El archivo {$filePath} no existe.");

            return static::CODE_ERROR;
        }

        $io->info("Ordenando archivo: {$file}");

        try {
            // Crear backup si se solicita
            if ($createBackup) {
                $backupPath = $filePath . '.backup.' . date('Y-m-d_H-i-s');
                copy($filePath, $backupPath);
                $io->success("Backup creado: {$backupPath}");
            }

            // Leer y procesar el archivo
            $content = file_get_contents($filePath);
            $sortedContent = $this->sortPoFile($content);

            // Escribir el archivo ordenado
            file_put_contents($filePath, $sortedContent);

            $io->success('Archivo ordenado alfabéticamente con éxito!');
            $io->info('Las entradas msgid están ahora ordenadas de A-Z');
        } catch (Exception $e) {
            $io->error('Error al procesar el archivo: ' . $e->getMessage());

            return static::CODE_ERROR;
        }

        return static::CODE_SUCCESS;
    }

    /**
     * Ordena el contenido de un archivo .po alfabéticamente por msgid
     *
     * @param string $content Contenido del archivo .po
     * @return string Contenido ordenado
     */
    private function sortPoFile(string $content): string
    {
        // Separar el header del resto del contenido
        $lines = explode("\n", $content);
        $header = [];
        $entries = [];
        $currentEntry = [];
        $inHeader = true;

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Detectar fin del header (primera línea msgid que no sea "")
            if ($inHeader && strpos($trimmedLine, 'msgid "') === 0 && $trimmedLine !== 'msgid ""') {
                $inHeader = false;
            }

            if ($inHeader) {
                $header[] = $line;
                continue;
            }

            // Si encontramos un nuevo msgid, guardar la entrada anterior
            if (strpos($trimmedLine, 'msgid "') === 0 && !empty($currentEntry)) {
                $entries[] = $this->processEntry($currentEntry);
                $currentEntry = [];
            }

            // Agregar línea a la entrada actual
            $currentEntry[] = $line;
        }

        // Agregar la última entrada
        if (!empty($currentEntry)) {
            $entries[] = $this->processEntry($currentEntry);
        }

        // Ordenar entradas por msgid
        usort($entries, function ($a, $b) {
            return strcasecmp($a['msgid'], $b['msgid']);
        });

        // Reconstruir el archivo
        $result = implode("\n", $header);

        foreach ($entries as $index => $entry) {
            // Limpiar líneas vacías al final de cada entrada
            $lines = $entry['lines'];
            while (!empty($lines) && trim(end($lines)) === '') {
                array_pop($lines);
            }

            $result .= "\n" . implode("\n", $lines);

            // Agregar una línea vacía entre entradas (excepto la última)
            if ($index < count($entries) - 1) {
                $result .= "\n";
            }
        }

        // Agregar una línea final
        $result .= "\n";

        return $result;
    }

    /**
     * Procesa una entrada individual extrayendo el msgid
     *
     * @param array $lines Líneas de la entrada
     * @return array Entrada procesada con msgid y líneas
     */
    private function processEntry(array $lines): array
    {
        $msgid = '';

        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            if (strpos($trimmedLine, 'msgid "') === 0) {
                // Extraer el msgid (sin comillas)
                preg_match('/msgid\s+"([^"]*)"/', $trimmedLine, $matches);
                $msgid = $matches[1] ?? '';
                break;
            }
        }

        return [
            'msgid' => $msgid,
            'lines' => $lines,
        ];
    }

    /**
     * Get the command description.
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return 'Ordena alfabéticamente las entradas de archivos .po por msgid';
    }
}
