<?php

namespace Wcs\TranslationMerger\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MergeCommand extends Command
{

    /**
     * Default locale code
     */
    const DEFAULT_LOCALE = 'en_US';

    /**
     * Input directory argument name
     */
    const INPUT_DIR_ARGUMENT = 'input-directory';

    /**
     * Locale argument name
     */
    const LOCALE_ARGUMENT = 'locale';

    protected function configure()
    {
        $this->setName('translation-merger:merge')
            ->setDescription(
                'Merge translations from magento i18n:collect command result with current translations')
            ->setDefinition([
                new InputArgument(
                    self::INPUT_DIR_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Input directory of collected Magento CSV file.'
                ),
                new InputArgument(
                    self::LOCALE_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Locale (Default: ' . self::DEFAULT_LOCALE . ')',
                    self::DEFAULT_LOCALE
                ),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $full_input_file_path = $input->getArgument(self::INPUT_DIR_ARGUMENT);
        $output_dir = dirname($full_input_file_path);
        $locale = $input->getArgument(self::LOCALE_ARGUMENT);
        $full_output_file_path = $output_dir . DIRECTORY_SEPARATOR . $locale . '.csv';

        if (!file_exists($full_input_file_path)) {
            $output->writeLn('<error>Could not find input file, check your path</error>');
            exit();
        }

        if (!file_exists($full_output_file_path)) {
            touch($full_output_file_path);
        }

        $iarr = $this->csvToArray(fopen($full_input_file_path, 'r'));
        $oarr = $this->csvToArray(fopen($full_output_file_path, 'r'));
        $ohandler = fopen($full_output_file_path, 'a');

        $translationsCount = 0;
        foreach ($iarr as $key => $value) {
            if (!in_array($value, $oarr)) {
                fputcsv($ohandler, [$value, $value]);
                $translationsCount++;
                $output->write('.');
            }
        }

        fclose($ohandler);

        $output->writeLn('<info>Done. New translations added: ' . $translationsCount . '</info>');
    }

    /**
     * @param $inputFile
     * @return array
     */
    private function csvToArray($inputFile)
    {
        $arr = [];
        while ($row = fgets($inputFile)) {
            $ex = explode(",", $row);
            if (count($ex) > 0)
                $arr[] = $ex[0];
        }
        fclose($inputFile);

        return $arr;
    }

}
