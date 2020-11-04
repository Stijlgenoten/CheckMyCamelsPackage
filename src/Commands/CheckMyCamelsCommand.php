<?php

namespace Stijlgenoten\CheckMyCamels\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckMyCamelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-my-camels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Case sensitive classname check';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $namespaces =  self::getDefinedNamespaces();

        foreach ($namespaces as $index => $namespace) {
            $currentNamespace  = rtrim($index, '\\');
            $classes = self::getClassesInNamespace($currentNamespace);

            $data = [
                'counted_classes' => self::getCountedClasses($classes),
                'camelErrors'   => self::getCamelCaseErrors($classes),
                'undefindedClasses' => self::getUndefindedClasses($classes),
            ];

            $this->info('Checking...');
            $this->info(' Checked: ' .$data['counted_classes'].' files');
            sleep(1);

            if ($data) {
                if (count($data['camelErrors'])) {
                    $this->info('');
                    $this->error('There are ' . count($data['camelErrors']) . ' strange camel(s) walking around in your app');
                    $this->info('');
                    foreach ($data['camelErrors'] as $classname) {
                        $this->info($classname);
                    }
                    $this->info('');
                }
                if (count($data['undefindedClasses'])) {
                    $this->info('');
                    $this->error('Found ' . count($data['undefindedClasses']) . ' wrong named camels');
                    $this->info('');
                    foreach ($data['undefindedClasses'] as $classname) {
                        $this->info($classname);
                    }
                    $this->info('');
                }

                if (!count($data['camelErrors']) && !count($data['undefindedClasses'])) {
                    $this->info('');
                    $this->info(' > No strange camels in the house!');
                    $this->info('');
                }
            }
        }

        $this->info('Done!');
    }

    public static function scanAllDir($dir)
    {
        $result = [];
        foreach (scandir($dir) as $filename) {
            if ('.' === $filename[0]) {
                continue;
            }
            $filePath = $dir . '/' . $filename;
            if (is_dir($filePath)) {
                foreach (self::scanAllDir($filePath) as $childFilename) {
                    $result[] = $filename . '/' . $childFilename;
                }
            } else {
                $result[] = $filename;
            }
        }

        return $result;
    }

    public static function getClassesInNamespace($namespace)
    {
        $files = self::scanAllDir(self::getNamespaceDirectory($namespace));

        $classes = array_map(function ($file) use ($namespace) {
            $file = str_replace('/', '\\', $file);

            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        return $classes;
    }

    public static function getCamelCaseErrors($classes)
    {
        return array_filter($classes, function ($possibleClass) {
            if (class_exists($possibleClass) && !trait_exists($possibleClass)) {
                if (!in_array($possibleClass, get_declared_classes())) {
                    return true;
                }
            }
        });
    }


    public static function getUndefindedClasses($classes)
    {
        return array_filter($classes, function ($possibleClass) {
            if (!class_exists($possibleClass) && !interface_exists($possibleClass) && !trait_exists($possibleClass)) {
                return true;
            }
        });
    }

    public static function getCountedClasses($classes){
        return count($classes);
    }


    public static function getDefinedNamespaces()
    {
        $composerJsonPath =  base_path() . '/' . 'composer.json';
        $composerConfig   = json_decode(file_get_contents($composerJsonPath));
        $psr              = 'psr-4';

        return (array) $composerConfig->autoload->$psr;
    }

    public static function getNamespaceDirectory($namespace)
    {
        $composerNamespaces = self::getDefinedNamespaces();

        $namespaceFragments          = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while ($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if (array_key_exists($possibleNamespace, $composerNamespaces)) {
                return realpath(base_path() . '/' . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments));
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        return false;
    }
}
