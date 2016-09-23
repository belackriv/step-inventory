<?php

namespace AppBundle\Library\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CsvDirectDownloadService extends ExcelDirectDownloadService
{
     /** @const string */
    const EXTENSION_CSV = 'csv';
    /** @const string */
    const MIME_TYPE_CSV = 'text/csv';

    public static function getDateTimeValue(\DateTime $dateTime)
    {
        return $dateTime->format('Y-m-d H:i:s');
    }

    public static function sendResponse($name, $data, Request $request)
    {
        $fileNameTemplate = '%s.%s';
        $fileName = sprintf(
            $fileNameTemplate,
            $name,
            self::EXTENSION_CSV
        );


        $contentTypeHeaderTemplate = '%s; name="%s"';
        $contentTypeHeader = sprintf(
            $contentTypeHeaderTemplate,
            self::MIME_TYPE_CSV,
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

        $fp = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        exit();
    }


}