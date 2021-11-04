<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use aspirantzhang\octopusModelCreator\TestCase;

class FileTest extends TestCase
{
    protected $file;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;
    protected $fileTypes;

    protected function setUp(): void
    {
        $this->snapPath = createPath(__DIR__, 'snapshots', 'lib', 'file', 'BasicModel');
        $this->fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        parent::setUp();
    }

    public function testCreate()
    {
        $config = [
            'name' => 'new-model',
            'title' => 'New Model',
        ];
        ModelCreator::file()->config($config)->create();

        foreach ($this->fileTypes as $type) {
            $filePath = createPath(base_path(), 'api', $type, 'NewModel') . '.php';
            $this->assertTrue($this->fileSystem->exists($filePath));
        }
    }

    /**
    * @depends testCreate
    */
    public function testUpdate()
    {
        $config = [
            'name' => 'new-model',
            'title' => 'New Model',
        ];
        ModelCreator::file()->config($config)->update($this->fieldsData, [
            'handleFieldValidation' => true,
            'handleFieldFilter' => true
        ]);
        $filePaths = [];
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'validate', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(root_path(), 'config', 'api', 'model', 'NewModel') . '.php';
        foreach ($filePaths as $filePath) {
            $this->assertTrue($this->fileSystem->exists($filePath));
        }
    }

    /**
    * @depends testUpdate
    */
    public function testRemove()
    {
        $config = [
            'name' => 'new-model',
            'title' => 'New Model',
        ];
        ModelCreator::file()->config($config)->remove();
        $filePaths = array_map(function ($type) {
            return createPath(base_path(), 'api', $type, 'NewModel') . '.php';
        }, $this->fileTypes);
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'validate', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(root_path(), 'config', 'api', 'model', 'NewModel') . '.php';
        foreach ($filePaths as $filePath) {
            $this->assertFalse($this->fileSystem->exists($filePath));
        }
    }
}
