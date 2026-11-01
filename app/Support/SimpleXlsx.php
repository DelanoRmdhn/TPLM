<?php

namespace App\Support;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Baca/tulis file Excel (.xlsx) sederhana: satu sheet, isi teks.
 * Cukup untuk template impor akun dan daftar kata sandi awal,
 * tanpa perlu package tambahan. Butuh ekstensi PHP "zip".
 */
class SimpleXlsx
{
    /**
     * Buat isi file .xlsx dari array baris. Baris pertama ditebalkan (judul kolom).
     * Semua kolom diformat Teks supaya NISN/NIP tidak berubah jadi angka.
     *
     * @param  array<int, array<int, string|int|null>>  $rows
     */
    public static function write(array $rows, string $sheetName = 'Sheet1'): string
    {
        $columnCount = max(1, ...array_map('count', $rows ?: [[]]));

        $sheetRows = '';
        foreach (array_values($rows) as $r => $row) {
            $style = $r === 0 ? 2 : 1;
            $cells = '';
            foreach (array_values($row) as $c => $value) {
                $ref = self::columnLetter($c).($r + 1);
                $text = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $cells .= "<c r=\"{$ref}\" t=\"inlineStr\" s=\"{$style}\"><is><t xml:space=\"preserve\">{$text}</t></is></c>";
            }
            $sheetRows .= '<row r="'.($r + 1).'">'.$cells.'</row>';
        }

        $cols = '<cols><col min="1" max="'.$columnCount.'" width="28" style="1" customWidth="1"/></cols>';
        $sheetName = htmlspecialchars($sheetName, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                .'</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                .'<sheets><sheet name="'.$sheetName.'" sheetId="1" r:id="rId1"/></sheets>'
                .'</workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                .'</Relationships>',
            'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
                .'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
                .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
                .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                .'<cellXfs count="3">'
                .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
                .'<xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
                .'<xf numFmtId="49" fontId="1" fillId="0" borderId="0" xfId="0" applyNumberFormat="1" applyFont="1"/>'
                .'</cellXfs>'
                .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
                .'</styleSheet>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .$cols.'<sheetData>'.$sheetRows.'</sheetData></worksheet>',
        ];

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive;
        $zip->open($tmp, ZipArchive::OVERWRITE);
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        $binary = file_get_contents($tmp);
        @unlink($tmp);

        return $binary;
    }

    /**
     * Baca sheet pertama. Hasil: [nomor baris Excel => [kolom0, kolom1, ...]].
     *
     * @return array<int, array<int, string>>
     */
    public static function read(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new RuntimeException('File bukan Excel (.xlsx) yang valid.');
        }

        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            foreach (self::xml($xml)->si as $si) {
                $shared[] = self::text($si);
            }
        }

        $sheet = $zip->getFromName(self::firstSheetPath($zip));
        $zip->close();

        if ($sheet === false) {
            throw new RuntimeException('Sheet di file Excel tidak ditemukan.');
        }

        $rows = [];
        foreach (self::xml($sheet)->sheetData->row as $row) {
            $values = [];
            $next = 0;
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                $index = $ref !== '' ? self::columnIndex($ref) : $next;
                $next = $index + 1;
                $type = (string) $cell['t'];

                $value = match ($type) {
                    's' => $shared[(int) $cell->v] ?? '',
                    'inlineStr' => self::text($cell->is),
                    default => (string) $cell->v,
                };

                $values[$index] = trim($value);
            }

            if ($values === []) {
                continue;
            }

            $filled = array_fill(0, max(array_keys($values)) + 1, '');
            $rows[(int) $row['r']] = array_replace($filled, $values);
        }

        return $rows;
    }

    private static function firstSheetPath(ZipArchive $zip): string
    {
        $workbook = $zip->getFromName('xl/workbook.xml');
        $rels = $zip->getFromName('xl/_rels/workbook.xml.rels');

        if ($workbook !== false && $rels !== false) {
            $first = self::xml($workbook)->sheets->sheet[0] ?? null;
            $id = $first?->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];

            foreach (self::xml($rels)->Relationship as $rel) {
                if ((string) $rel['Id'] === (string) $id) {
                    $target = ltrim((string) $rel['Target'], '/');

                    return str_starts_with($target, 'xl/') ? $target : 'xl/'.$target;
                }
            }
        }

        return 'xl/worksheets/sheet1.xml';
    }

    private static function xml(string $content): SimpleXMLElement
    {
        $xml = simplexml_load_string($content, SimpleXMLElement::class, LIBXML_NONET);

        if ($xml === false) {
            throw new RuntimeException('Isi file Excel rusak.');
        }

        return $xml;
    }

    /** Ambil teks dari <si>/<is>, termasuk teks berformat (<r><t>). */
    private static function text(SimpleXMLElement $node): string
    {
        if (isset($node->t)) {
            return (string) $node->t;
        }

        $text = '';
        foreach ($node->r as $run) {
            $text .= (string) $run->t;
        }

        return $text;
    }

    private static function columnIndex(string $ref): int
    {
        $letters = preg_replace('/\d/', '', $ref);
        $index = 0;
        foreach (str_split($letters) as $char) {
            $index = $index * 26 + (ord($char) - 64);
        }

        return $index - 1;
    }

    private static function columnLetter(int $index): string
    {
        $letter = '';
        for ($index++; $index > 0; $index = intdiv($index - 1, 26)) {
            $letter = chr(65 + ($index - 1) % 26).$letter;
        }

        return $letter;
    }
}
