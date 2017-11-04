<?php

use \Eleggua\Wkhtmltopdf\Wkhtmltopdf;
use Eleggua\Wkhtmltopdf\CmdBuilder;

/**
 * Class Vendor_Wkhtmltopdf
 */
class Vendor_Wkhtmltopdf extends Wkhtmltopdf
{
    const PATH = TMPPATH;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = array())
    {
        $cmd = new CmdBuilder();
        $renderer = new Vendor_PdfRenderer();

        parent::__construct($cmd, $renderer);
    }
}
