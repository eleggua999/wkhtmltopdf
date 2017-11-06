<?php

namespace Eleggua\Wkhtmltopdf;

/**
 * Class Wkhtmltopdf
 * @package Eleggua\Wkhtmltopdf
 */
class Wkhtmltopdf
{
    const PATH = '../../../../../';

    /**
     * available page orientations
     */
    const ORIENTATION_PORTRAIT = 'Portrait';    // vertical
    const ORIENTATION_LANDSCAPE = 'Landscape';  // horizontal

    /**
     * page sizes
     */
    const SIZE_A4 = 'A4';
    const SIZE_LETTER = 'letter';

    /**
     * file get modes
     */
    const MODE_DOWNLOAD = 0;
    const MODE_STRING = 1;
    const MODE_EMBEDDED = 2;
    const MODE_SAVE = 3;
    const MODE_SAVE_AND_DOWNLOAD = 4;
    const MODE_SAVE_AND_RETURN = 5;

    /**
     * @var CmdBuilder
     */
    protected $cmd;

    /**
     * @var PdfRenderer
     */
    protected $renderer;

    /**
     * @var string
     */
    private $html;

    /**
     * Wkhtmltopdf constructor.
     * @param CmdBuilder $cmd
     * @param PdfRenderer $renderer
     */
    public function __construct(CmdBuilder $cmd, PdfRenderer $renderer)
    {
        $this->cmd = $cmd;
        $this->renderer = $renderer;
    }

    /**
     * @param int $mode
     * @param string $filename
     * @param bool $unlink
     * @return mixed
     * @throws \Exception
     */
    public function output($mode, $filename, $unlink = true)
    {
        $html = $this->html;
        $command = $this->buildCommand();

        switch ($mode) {
            case self::MODE_DOWNLOAD:
                if (!headers_sent()) {
                    $result = $this->renderer->render($command, $html);
                    header("Content-Description: File Transfer");
                    header("Cache-Control: public; must-revalidate, max-age=0");
                    header("Pragme: public");
                    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                    header("Last-Modified: " . gmdate('D, d m Y H:i:s') . " GMT");
                    header("Content-Type: application/force-download");
                    header("Content-Type: application/octec-stream", false);
                    header("Content-Type: application/download", false);
                    header("Content-Type: application/pdf", false);
                    header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
                    header("Content-Transfer-Encoding: binary");

                    echo $result;

                    exit();
                } else {
                    throw new \Exception("Headers already sent");
                }
                break;
            case self::MODE_STRING:
                return $this->renderer->render($command, $html);
                break;
            case self::MODE_EMBEDDED:
                if (!headers_sent()) {
                    $result = $this->renderer->render($command, $html);;
                    header("Content-type: application/pdf");
                    header("Cache-control: public, must-revalidate, max-age=0");
                    header("Pragme: public");
                    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                    header("Last-Modified: " . gmdate('D, d m Y H:i:s') . " GMT");
                    header('Content-Disposition: inline; filename="' . basename($filename) . '";');

                    echo $result;

                    exit();
                } else {
                    throw new \Exception("Headers already sent");
                }
                break;
            case self::MODE_SAVE:
                file_put_contents($filename, $this->renderer->render($command, $html));

                break;
            case self::MODE_SAVE_AND_DOWNLOAD:

                $result = $this->renderer->render($command, $html);;

                file_put_contents($filename, $result);

                if (!headers_sent()) {
                    header("Content-Description: File Transfer");
                    header("Cache-Control: public; must-revalidate, max-age=0");
                    header("Pragme: public");
                    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
                    header("Last-Modified: " . gmdate('D, d m Y H:i:s') . " GMT");
                    header("Content-Type: application/force-download");
                    header("Content-Type: application/octec-stream", false);
                    header("Content-Type: application/download", false);
                    header("Content-Type: application/pdf", false);
                    header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
                    header("Content-Transfer-Encoding: binary");

                    echo file_get_contents($filename);

                    // delete pdf
                    if ($unlink) {
                        unlink($filename);
                    }

                    exit();
                } else {
                    throw new \Exception("Headers already sent");
                }

                break;
            case self::MODE_SAVE_AND_RETURN:
                $result = $this->renderer->render($command, $html);;
                file_put_contents($filename, $result);
                //unlink($this->_filename);
                return $result;
                break;
            default:
                throw new \Exception("Mode: " . $mode . " is not supported");
        }
    }

    /**
     * @return string
     */
    protected function buildCommand()
    {
        return $this->cmd
            ->build()
            ->getCommand()
            ;
    }

    /**
     * @param string $orientation
     * @return Wkhtmltopdf
     */
    public function setOrientation($orientation)
    {
        // TODO: Add invalid value exception
        $this->cmd->orientation = $orientation;
        return $this;
    }

    /**
     * @param string $pageSize
     * @return Wkhtmltopdf
     */
    public function setPageSize($pageSize)
    {
        // TODO: Add invalid value exception
        $this->cmd->pageSize = $pageSize;
        return $this;
    }

    /**
     * @param bool $param
     * @return Wkhtmltopdf
     */
    public function setToc($param)
    {
        if ($param) {
            $this->cmd->toc = " --toc";
        } else {
            $this->cmd->toc = "";
        }
        return $this;
    }

    /**
     * @param int $copies
     * @return Wkhtmltopdf
     */
    public function setCopies($copies)
    {
        $this->cmd->copies = $copies;
        return $this;
    }

    /**
     * @param bool $param
     * @return Wkhtmltopdf
     */
    public function setDisableAutoScaling($param)
    {
        if ($param) {
            $this->cmd->autoScaling = " --disable-smart-shrinking";
        } else {
            $this->cmd->autoScaling = "";
        }
        return $this;
    }

    /**
     * @param bool $param
     * @return Wkhtmltopdf
     */
    public function setGrayscale($param)
    {
        if ($param) {
            $this->cmd->grayscale = " --grayscale";
        } else {
            $this->cmd->grayscale = "";
        }
        return $this;
    }

    /**
     * @param string $footerHtml
     * @return Wkhtmltopdf
     */
    public function setFooterHtml($footerHtml)
    {
        $this->cmd->footerHtml = " --footer-html " . $footerHtml;
        return $this;
    }

    /**
     * @param int $marginTop
     * @return Wkhtmltopdf
     */
    public function setMarginTop($marginTop)
    {
        $this->cmd->marginTop = $marginTop;
        return $this;
    }

    /**
     * @param int $marginBottom
     * @return Wkhtmltopdf
     */
    public function setMarginBottom($marginBottom)
    {
        $this->cmd->marginBottom = $marginBottom;
        return $this;
    }

    /**
     * @param int $marginLeft
     * @return Wkhtmltopdf
     */
    public function setMarginLeft($marginLeft)
    {
        $this->cmd->marginLeft = $marginLeft;
        return $this;
    }

    /**
     * @param int $marginRight
     * @return Wkhtmltopdf
     */
    public function setMarginRight($marginRight)
    {
        $this->cmd->marginRight = $marginRight;
        return $this;
    }

    /**
     * @param string $pageHeight
     * @return Wkhtmltopdf
     */
    public function setPageHeight($pageHeight)
    {
        $this->cmd->pageHeight = $pageHeight;
        return $this;
    }


    /**
     * @param string $pageWidth
     * @return Wkhtmltopdf
     */
    public function setPageWidth($pageWidth)
    {
        $this->cmd->pageWidth = $pageWidth;
        return $this;
    }

    /**
     * Set all page margins
     * @param int $margin
     * @return Wkhtmltopdf
     */
    public function setMargins($margin)
    {
        $this->cmd->marginTop
            = $this->cmd->marginBottom
            = $this->cmd->marginLeft
            = $this->cmd->marginRight
            = $margin;

        return $this;
    }

    /**
     * @param string $html
     * @return Wkhtmltopdf
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }
}