<?php

namespace Stijlgenoten\CheckMyCamels\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckMyCamels extends GeneratorCommand
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
            $unreadableClasses = self::getClassesInNamespace($currentNamespace);

            // get files
            $this->info(' Checking: ' .$unreadableClasses['counted_classes'].' files');
            sleep(1);

            if ($unreadableClasses) {
                if (count($unreadableClasses['camalErrors'])) {
                    $this->info('');
                    $this->error('There are ' . count($unreadableClasses['camalErrors']) . ' strange camel(s) walking around in your app');
                    $this->info('');
                    foreach ($unreadableClasses['camalErrors'] as $classname) {
                        $this->info($classname);
                    }
                    $this->info('');
                }
                if (count($unreadableClasses['classNotFound'])) {
                    $this->info('');
                    $this->error('Found ' . count($unreadableClasses['classNotFound']) . ' wrong named camels');
                    $this->info('');
                    foreach ($unreadableClasses['classNotFound'] as $classname) {
                        $this->info($classname);
                    }
                    $this->info('');
                }

                if (!count($unreadableClasses['camalErrors']) && !count($unreadableClasses['camalErrors'])) {
                    $this->info('');
                    $this->info(' > No strange camels in the house!');
                    $this->info('');
                }
            }
        }
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

        return [
            'counted_classes' => count($classes),
            'camalErrors'   => self::getCalelErrors($classes),
            'classNotFound' => self::classNotFound($classes),
        ];
    }

    private static function getCalelErrors($classes)
    {
        return array_filter($classes, function ($possibleClass) {
            if (class_exists($possibleClass) && !trait_exists($possibleClass)) {
                if (!in_array($possibleClass, get_declared_classes())) {
                    return true;
                }
            }
        });
    }


    private static function classNotFound($classes)
    {
        return array_filter($classes, function ($possibleClass) {
            if (!class_exists($possibleClass) && !trait_exists($possibleClass)) {
                return true;
            }
        });
    }

    private static function getDefinedNamespaces()
    {
        $composerJsonPath =  base_path() . '/' . 'composer.json';
        $composerConfig   = json_decode(file_get_contents($composerJsonPath));
        $psr              = 'psr-4';

        return (array) $composerConfig->autoload->$psr;
    }

    private static function getNamespaceDirectory($namespace)
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
