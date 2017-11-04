<?php

use Eleggua\Wkhtmltopdf\PdfRenderer;

/**
 * Class Vendor_PdfRenderer
 */
class Vendor_PdfRenderer extends PdfRenderer
{
    /**
     * {@inheritdoc}
     */
    protected function log($message)
    {
        Kohana::$log->add(Kohana_Log::ERROR, $message);
    }
}
