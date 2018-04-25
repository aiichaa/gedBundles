<?php
/**
 * Created by PhpStorm.
 * User: Aicha
 * Date: 21/06/2017
 * Time: 08:24
 */
namespace Orca\EditiqueBundle\Utils\LibreOfficeWrapper;

use Orca\EditiqueBundle\Utils\LibreOfficeWrapper\Exception\CommandException;
use Symfony\Component\HttpFoundation\File\File;

class LibreOffice
{

    /**
     * Project placement
     *
     * @var string
     */
    protected $placement;

    public function __construct($placement)
    {
        $path = 'jodconverter-core-3.0-beta-4/lib/jodconverter-core-3.0-beta-4.jar'; //Path to JodConverter jar
        $this->placement = $placement;
        if ($this->placement == 'prod') // If project placement is prod Linux (generate with JodConverter)
            $path = 'java -jar ' . $path;
        else if(DIRECTORY_SEPARATOR == '\\' and $this->placement == 'local') // If operating system is Windows and project placement is local (generate with LibreOffice)
//            $path = '"C:\Program Files (x86)\LibreOffice 5\program\soffice.exe"';
            $path = '"C:\Program Files\LibreOffice 5\program\soffice.exe"'; //Aicha windows env
        else // If project placement is r7 [CentOs] (generate with LibreOffice)
            $path = 'libreoffice';

        $this->path = $path;
    }


    /**
     * Get version information
     *
     * @return array
     */
    public function getVersion()
    {
        return $this->execute('-version');
    }

    /**
     * Convert docx to pdf
     *
     * @param string $filename
     *
     */
    public function convert_docx_to_pdf($filename,$outdir)
    {

        if ($this->placement == 'prod') // If project placement is prod Linux (generate with JodConverter)
        {
            $this->execute(
                sprintf(
                    ' %s %s',
                    \escapeshellarg($filename),
                    $outdir
                )
            );
        }
        else
        {
            $this->execute(
                sprintf(
                    ' --headless -convert-to pdf %s -outdir %s',
                    \escapeshellarg($filename),
                    $outdir
                )
            );
        }

    }

    /**
     * Execute command and return output
     *
     * @param string $parameters
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function execute($parameters)
    {

        if ($this->placement == 'prod') // If project placement is prod Linux (generate with JodConverter)
        {
            $command = sprintf(
                '%s %s',
                $this->path,
                $parameters
            );
        }
        else
        {
            $command = sprintf(
                '%s %s 2>&1',
                $this->path,
                $parameters
            );
        }

        \exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw CommandException::factory($command, $output);
            var_dump("erreur libreOffice");
        }

        return $output;
    }
}