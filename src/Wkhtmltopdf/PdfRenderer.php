<?php
/**
 * @copyright C UAB NFQ Technologies 2017
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 *
 */

namespace Eleggua\Wkhtmltopdf;

class PdfRenderer
{
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

    const HTML_EXT = 'html';
    const PDF_EXT = 'pdf';

    /**
     * @param string $extension
     * @return string
     */
    public function generateFilename($extension)
    {
        do {
            $filename = Wkhtmltopdf::PATH . mt_rand() . '.' . $extension;
        } while (file_exists($filename));

        return $filename;
    }

    /**
     * Executes command
     *
     * @author aur1mas <aur1mas@devnet.lt>
     * @param string $cmd command to execute
     * @return array
     */
    protected function exec($cmd)
    {
        $response = array(
            'stdout' => '',
            'stderr' => '',
            'return' => 0
        );

        $temp_pdf_path = $this->generateFilename('pdf');

        $cmd .= ' > ' . $temp_pdf_path . ' 2>/dev/null';

        exec($cmd, $output, $return_value);
        if ($return_value) {
            $response['return'] = $return_value;
            $response['stderr'] = implode(';', $output);
            return $response;
        }

        $response['stdout'] = file_get_contents($temp_pdf_path);
        if ($response['stdout'] === false) {
            $response['return'] = 1;
            $response['stderr'] = 'no PDF file';
            return $response;
        }

        unlink($temp_pdf_path);
        return $response;
    }

    /**
     * @author aur1mas <aur1mas@devnet.lt>
     * @param string $command
     * @param string $html
     * @param string $url
     * @return mixed
     * @throws \Exception
     */
    public function render($command, $html = '', $url = '')
    {
        try {
            if (strlen($html) === 0 && empty($url)) {
                throw new \Exception("HTML content or source URL not set");
            }

            if ($url) {
                $input = $url;
            } else {
                $filename = $this->generateFilename('html');
                file_put_contents($filename, $html);
                chmod($filename, 0777);
                $input = $filename;
            }

            $command = str_replace('%input%', $input, $command);
            $content = $this->exec($command);

            // WKHTMLTOPDF occasionally returns error 134. Yet it works on the second attempt.
            if ($content['return'] == 134) {
                $content = $this->exec($command);
            }

            if ($content['return'] !== 0) {
                throw new \Exception('WKHTMLTOPDF exec failed');
            }

            if (strlen($content['stdout']) === 0) {
                throw new \Exception("WKHTMLTOPDF didn't return any data");
            }
        } catch (\Exception $e) {
            $stderr = isset($content['stderr']) ? $content['stderr'] : 'no stderr';
            $stdout = isset($content['stdout']) ? $content['stdout'] : 'no stdout';
            $return = isset($content['return']) ? $content['return'] : 'no return';
            $this->log(strstr('WKHTMLTOPDF failed stderr: :stderr; stdout: :stdout; return: :return; cmd: :cmd',
                array(':stderr' => $stderr, ':stdout' => $stdout, ':return' => $return, ':cmd' => $command)));
            throw $e;
        }

        return $content['stdout'];
    }

    /**
     * @param string $message
     */
    protected function log($message)
    {
        error_log($message);
    }
}