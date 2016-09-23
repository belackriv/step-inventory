<?php

namespace AppBundle\Library\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use \PHPExcel;
Use \PHPExcel_IOFactory;
use \PHPExcel_Style_Alignment;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell_DataType;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Shared_Date;

class ExcelDirectDownloadService
{
    /*
        $data format show be [
            [
                [$valueRow1Col1, $self::FORMAT],
                [$valueRow1Col2, $self::FORMAT],
            ],[
                [$valueRow2Col1, $self::FORMAT],
                [$valueRow2Col2, $self::FORMAT],
            ]
        ]
    */

    /** @const string */
    const EXTENSION_EXCEL = 'xlsx';
    /** @const string */
    const MIME_TYPE_EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    const FORMAT_NONE = 0;
    const FORMAT_STRING = 1;
    const FORMAT_INTEGER = 2;
    const FORMAT_DECIMAL_TWO = 3;
    const FORMAT_PERCENT = 4;
    const FORMAT_DATETIME = 5;
    const FORMAT_DATE = 6;
    const FORMAT_TIME = 7;

    /** @var array */
    public static $formats = [
        self::FORMAT_NONE => 'None',
        self::FORMAT_STRING => 'String',
        self::FORMAT_INTEGER => 'Integer',
        self::FORMAT_DECIMAL_TWO => 'Two Decimals',
        self::FORMAT_PERCENT => 'Percent',
        self::FORMAT_DATETIME => 'DateTime',
        self::FORMAT_DATE => 'Date',
        self::FORMAT_TIME => 'Time'
    ];

    public static function getFormatFromName($name)
    {
        $name = str_replace(' ', '', strtolower($name));
        foreach(self::$formats as $format => $formatName){
            if($name === str_replace(' ', '', strtolower($formatName))){
                return $format;
            }
        }
        return 0;
    }

    public static function sendResponse($name, $data, Request $request)
    {
        $objPHPExcel = self::getExcelFromData($name, $data);

        $fileNameTemplate = '%s.%s';
        $fileName = sprintf(
            $fileNameTemplate,
            $name,
            self::EXTENSION_EXCEL
        );


        $contentTypeHeaderTemplate = '%s; name="%s"';
        $contentTypeHeader = sprintf(
            $contentTypeHeaderTemplate,
            self::MIME_TYPE_EXCEL,
            $fileName
        );

        $contentDispositionHeaderTemplate = 'attachment; filename="%s"';
        $contentDispositionHeader = sprintf(
            $contentDispositionHeaderTemplate,
            $fileName
        );

        $response = new Response();
        $response->headers->set('Content-Type', $contentTypeHeader);
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Content-Disposition', $contentDispositionHeader);
        $response->prepare($request);
        $response->sendHeaders();

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }

    public static function getExcelFromData($name, $data)
    {
        /*
        $headerData = [
            'Report',
            '"'.$this->report->getName().'"',
            'From',
            $input->getStartDate()->format('Y-m-d'),
            'To',
            $input->getEndDate()->format('Y-m-d')
        ];
        */
        $headerStyleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );

        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
        $cacheSettings = array( 'memoryCacheSize'  => '512MB' );
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

        $default_locale = \Locale::getDefault()?:'en-us';
        $validLocale = PHPExcel_Settings::setLocale($default_locale);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getSheet(0)->setTitle(ucwords($name));
/*
        $objPHPExcel->getSheet(0)->setCellValueByColumnAndRow(0, 1, implode(' ', $headerData));
        $objPHPExcel->getSheet(0)->mergeCells('A1:F1');
        $headerRowIndex = 2;
*/
        $headerRowIndex = 1;
        foreach($data as $rowIndex => $row){
            foreach($row as $colIndex => $value){
                $castedValue = self::getCastedValueFromFormat($value[0], $value[1]);
                $dataType = self::getExcelDataTypeFromFormat($value[1]);
                if($castedValue === null){
                    $dataType = PHPExcel_Cell_DataType::TYPE_NULL;
                }
                $numberFormat = self::getExcelNumberFormatFromFormat($value[1]);
                $objPHPExcel->getSheet(0)->setCellValueExplicitByColumnAndRow($colIndex, $rowIndex+$headerRowIndex, $castedValue, $dataType);
                $objPHPExcel->getSheet(0)->getCellByColumnAndRow($colIndex, $rowIndex+$headerRowIndex)
                    ->getStyle()->getNumberFormat()->setFormatCode($numberFormat);
            }
        }
        $lastCol = $objPHPExcel->getSheet(0)->getHighestColumn($headerRowIndex);
        $objPHPExcel->getSheet(0)->getStyle('A1:'.$lastCol.$headerRowIndex)->applyFromArray($headerStyleArray);
        $objPHPExcel->getSheet(0)->setAutoFilter('A'.$headerRowIndex.':'.$lastCol.$objPHPExcel->getSheet(0)->getHighestRow());
        $lastCol++;
        for ($col = 'A'; $col != $lastCol; $col++) {
            $objPHPExcel->getSheet(0)->getColumnDimension($col)->setAutoSize(true);
        }

        $objPHPExcel->setActiveSheetIndex(0);
        return $objPHPExcel;
    }

    public static function getCastedValueFromFormat($value, $format)
    {
        if($value === null){
            return null;
        }
        switch($format){
            case self::FORMAT_NONE:
                return sprintf('%s', $value);
            case self::FORMAT_STRING:
                return sprintf('%s', $value);
            case self::FORMAT_INTEGER:
                return (int)sprintf('%d', $value);
            case self::FORMAT_DECIMAL_TWO:
                return (float)sprintf('%0.2f', $value);
            case self::FORMAT_PERCENT:
                return (float)sprintf('%.4f%%', $value);
            case self::FORMAT_DATETIME:
            case self::FORMAT_DATE:
            case self::FORMAT_TIME:
                if(!is_a($value, 'DateTime')){
                    $value = new \DateTime($value);
                }
                return static::getDateTimeValue($value);
            default:
                return sprintf('%s', $value);
        }
    }

    public static function getDateTimeValue(\DateTime $dateTime)
    {
        return PHPExcel_Shared_Date::PHPToExcel($dateTime);
    }

    public static function getExcelDataTypeFromFormat($format)
    {
/*
    valid types:
PHPExcel_Cell_DataType::TYPE_BOOL
PHPExcel_Cell_DataType::TYPE_ERROR
PHPExcel_Cell_DataType::TYPE_FORMULA
PHPExcel_Cell_DataType::TYPE_INLINE
PHPExcel_Cell_DataType::TYPE_NULL
PHPExcel_Cell_DataType::TYPE_NUMERIC
PHPExcel_Cell_DataType::TYPE_STRING
*/
        switch($format){
            case self::FORMAT_NONE:
                return PHPExcel_Cell_DataType::TYPE_STRING;
            case self::FORMAT_STRING:
                return PHPExcel_Cell_DataType::TYPE_STRING;
            case self::FORMAT_INTEGER:
                return PHPExcel_Cell_DataType::TYPE_NUMERIC;
            case self::FORMAT_DECIMAL_TWO:
                return PHPExcel_Cell_DataType::TYPE_NUMERIC;
            case self::FORMAT_PERCENT:
                return PHPExcel_Cell_DataType::TYPE_NUMERIC;
            case self::FORMAT_DATETIME:
                return PHPExcel_Cell_DataType::TYPE_NUMERIC;
            case self::FORMAT_DATE:
                return PHPExcel_Cell_DataType::TYPE_NUMERIC;
             case self::FORMAT_TIME:
                return PHPExcel_Cell_DataType::TYPE_NUMERIC;
             default:
                return PHPExcel_Cell_DataType::TYPE_STRING;
        }
    }

    public static function getExcelNumberFormatFromFormat($format)
    {
/*
    valid number formats
PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD
PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE
PHPExcel_Style_NumberFormat::FORMAT_DATE_DATETIME
PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY
PHPExcel_Style_NumberFormat::FORMAT_DATE_DMMINUS
PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYMINUS
PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH
PHPExcel_Style_NumberFormat::FORMAT_DATE_MYMINUS
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME1
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME4
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME5
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME6
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME7
PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME8
PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD
PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH
PHPExcel_Style_NumberFormat::FORMAT_GENERAL
PHPExcel_Style_NumberFormat::FORMAT_NUMBER
PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00
PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2
PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
*/
         switch($format){
            case self::FORMAT_NONE:
                return PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
            case self::FORMAT_STRING:
                return PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
            case self::FORMAT_INTEGER:
                return PHPExcel_Style_NumberFormat::FORMAT_NUMBER;
            case self::FORMAT_DECIMAL_TWO:
                return PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00;
            case self::FORMAT_PERCENT:
                return PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
            case self::FORMAT_DATETIME:
                //return PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2;
                return 'm/d/yyyy h:mm AM/PM';
            case self::FORMAT_DATE:
                return PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD;
            case self::FORMAT_TIME:
                return PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME2;
            default:
                return PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
        }
    }
}