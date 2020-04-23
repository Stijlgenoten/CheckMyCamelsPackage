<?php

namespace Stijlgenoten\CheckMyCamels\Tests\Unit;

use Stijlgenoten\CheckMyCamels\Commands\CheckMyCamelsCommand;
use Stijlgenoten\CheckMyCamels\Tests\TestCase;

class CanLoadNamespaceTest extends TestCase
{
    /** @test */
    public function it_can_load_the_app_namespace()
    {
        $namespaces =  CheckMyCamelsCommand::getDefinedNamespaces();
        $this->assertIsArray($namespaces);
    }

    /** @test */
    public function it_can_load_classes_from_namespace()
    {
        $namespaces =  CheckMyCamelsCommand::getDefinedNamespaces();
        foreach ($namespaces as $index => $namespace) {
            $currentNamespace  = rtrim($index, '\\');
            $classes           = CheckMyCamelsCommand::getClassesInNamespace($currentNamespace);
            $this->assertIsArray($classes);
        }
    }

    /** @test */
    public function it_can_count_classes()
    {
        $currentNamespace   = rtrim(key(CheckMyCamelsCommand::getDefinedNamespaces()));
        $classes            = CheckMyCamelsCommand::getClassesInNamespace($currentNamespace);
        $OutputArray        = CheckMyCamelsCommand::getCountedClasses($classes);
        $this->assertIsNumeric($OutputArray);
    }


    /** @test */
    public function it_can_get_camel_case_errors()
    {
        $currentNamespace   = rtrim(key(CheckMyCamelsCommand::getDefinedNamespaces()));
        $classes            = CheckMyCamelsCommand::getClassesInNamespace($currentNamespace);
        $OutputArray        = CheckMyCamelsCommand::getCamelCaseErrors($classes);
        $this->assertIsArray($OutputArray);
    }


        /** @test */
    public function it_can_get_undefinded_classes()
    {
        $currentNamespace   = rtrim(key(CheckMyCamelsCommand::getDefinedNamespaces()));
        $classes            = CheckMyCamelsCommand::getClassesInNamespace($currentNamespace);
        $OutputArray        = CheckMyCamelsCommand::getUndefindedClasses($classes);
        $this->assertIsArray($OutputArray);
    }
}
