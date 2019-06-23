<?php

namespace App\Lib\Exporter;


/**
 * Class CSVExporter
 * @package App\Lib\Exporter
 */
class CSVExporter implements Exportable
{

    protected $filename, $data, $field, $status = false, $file;
    /**
     * @var string
     */
    protected $folder = 'reports';

    /**
     * CSVExporter constructor.
     * @param $data
     * @param $field
     * @param string $filename
     */
    public function __construct($data, $field, $filename = '')
    {
        $filename = $filename == '' ? random_string() : $filename;
        $this->filename = $filename;
        $this->data = $data;
        $this->field = $field;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function export()
    {
        $path = storage_path($this->folder);

        $fileName = $this->filename . uniqid(date('_Y_m_d_')) . '.csv';
        $fullPath = $path . DIRECTORY_SEPARATOR . $fileName;
        if (!is_dir($path)) {
            mkdir($path, 777);
        }
        try {
            $file = fopen($fullPath, 'w');

            fputcsv($file, $this->field);

            foreach ($this->data as $key => $d) {
                if ($key !== 'request' && $key !== 'table' && !is_string($d))
                    fputcsv($file, (array)$d);
            }
            fclose($file);
            $this->status = true;
            $this->file = $fullPath;

        } catch (\Exception $e) {
            $this->status = false;
            throw  $e;
        }
        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getFile()
    {
        if ($this->status) {
            return $this->file;
        }
        throw new \Exception('File not Formed Status with name ' . $this->filename);
    }

    /**
     * @param array $field
     * @param $data
     * @param $fileName
     * @return bool|string
     */
    public static function arrayToCSV(array $field, $data, $fileName)
    {
        if (!file_exists(storage_path('reports'))) {
            mkdir(storage_path('reports'), 0777, true);
        }
        $path = storage_path('reports');

        $fileName = $fileName . uniqid(date('_Y_m_d_H_i_s_')) . '.csv';
        $fullPath = $path . DIRECTORY_SEPARATOR . $fileName;

        try {
            $file = fopen($fullPath, 'w');

            if (array_key_exists('request', $data)) {
                $criteria = $data['request'];
                unset($data['request']);
                fputcsv($file, ['Search Criteria','Value']);
                foreach ($criteria as $key => $value)
                {
                    if(is_array($value) && count($value)>0)
                        $value=implode(',',$value);
                    fputcsv($file, [$key, $value]);
                }
            }
            fputcsv($file, []);

            fputcsv($file, $field);
            foreach ($data as $d) {
                if (!is_string($d))
                    fputcsv($file, $d);
            }
            fclose($file);
            return $fileName;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param array $field
     * @param $data
     * @param $fileName
     * @return bool|string
     */
    public static function highVolumeSpreadSheet(array $codes, array $field, $data, $fileName)
    {
        if (!file_exists(storage_path('reports'))) {
            mkdir(storage_path('reports'), 0777, true);
        }
        $path = storage_path('reports');

        $fileName = $fileName . uniqid(date('_Y_m_d_H_i_s_')) . '.csv';
        $fullPath = $path . DIRECTORY_SEPARATOR . $fileName;

        try {
            $file = fopen($fullPath, 'w');

            if (array_key_exists('request', $data)) {
                $criteria = $data['request'];
                unset($data['request']);
                fputcsv($file, ['Search Criteria', 'Value']);
                foreach ($criteria as $key => $value)
                {
                    if(is_array($value) && count($value)>0)
                        $value=implode(',',$value);
                    fputcsv($file, [$key, $value]);
                }
            }
            fputcsv($file, []);
            $c = ["", ""];
            $codes = array_merge($c, $codes);
            $field = array_merge($c, $field);
            fputcsv($file, $codes);
            fputcsv($file, $field);
            foreach ($data as $d) {
                $d = array_merge($c, $d);
                if (!is_string($d))
                    fputcsv($file, $d);
            }
            fclose($file);
            return $fileName;
        } catch (\Exception $e) {
            return false;
        }
    }

}
