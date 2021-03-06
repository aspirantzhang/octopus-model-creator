<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class BasicModelTest extends BaseCase
{
    protected $basicModel;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;
    protected $fileTypes;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'BasicModel');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'BasicModel', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'BasicModel');
        $this->fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        $this->basicModel = new BasicModel();
        parent::setUp();
    }

    public function testCreateBasicModelFile()
    {
        $this->basicModel->init($this->singleMainTableConfig)->createBasicModelFile();

        foreach ($this->fileTypes as $type) {
            $filePath = createPath(base_path(), 'api', $type, 'UnitTest') . '.php';
            $snapshotPath = createPath($this->snapPath, 'api', $type, 'UnitTest') . '.php.snap';
            $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('unable to get file content: filePath=' . createPath($this->prodStubPath, 'notExist.stub'));
        $this->basicModel->init($this->singleMainTableConfig)->createBasicModelFile(['notExist']);
    }

    public function testRemoveBasicModelFile()
    {
        $this->basicModel->init($this->singleMainTableConfig)->removeBasicModelFile();
        $filePaths = array_map(function ($type) {
            return createPath(base_path(), 'api', $type, 'UnitTest') . '.php';
        }, $this->fileTypes);
        foreach ($filePaths as $filePath) {
            $this->assertFalse($this->fileSystem->exists($filePath));
        }
    }

    public function testMainTableOfCategoryBasicModel()
    {
        $this->basicModel->init($this->mainTableOfCategoryTypeConfig)->createBasicModelFile(['controller', 'model']);
        // controller
        $controllerFilePath = createPath(base_path(), 'api', 'controller', 'MainTableOfCategory') . '.php';
        $controllerSnapshotPath = createPath($this->snapPath, 'api', 'controller', 'MainTableOfCategory') . '.php.snap';
        $this->assertTrue(matchSnapshot($controllerFilePath, $controllerSnapshotPath));
        // model
        $modelFilePath = createPath(base_path(), 'api', 'model', 'MainTableOfCategory') . '.php';
        $modelSnapshotPath = createPath($this->snapPath, 'api', 'model', 'MainTableOfCategory') . '.php.snap';
        $this->assertTrue(matchSnapshot($modelFilePath, $modelSnapshotPath));
    }

    public function testCategoryTableOfCategoryBasicModel()
    {
        $this->basicModel->init($this->categoryTableOfCategoryTypeConfig)->createBasicModelFile(['model']);
        // model
        $modelFilePath = createPath(base_path(), 'api', 'model', 'CategoryTableOfCategory') . '.php';
        $modelSnapshotPath = createPath($this->snapPath, 'api', 'model', 'CategoryTableOfCategory') . '.php.snap';
        $this->assertTrue(matchSnapshot($modelFilePath, $modelSnapshotPath));
        // pivot
        $pivotFilePath = createPath(base_path(), 'api', 'model', 'PivotMainTableOfCategoryCategory') . '.php';
        $pivotSnapshotPath = createPath($this->snapPath, 'api', 'model', 'PivotMainTableOfCategoryCategory') . '.php.snap';
        $this->assertTrue(matchSnapshot($pivotFilePath, $pivotSnapshotPath));
    }

    public function testRemoveCategoryTableOfCategory()
    {
        $this->basicModel->init($this->categoryTableOfCategoryTypeConfig)->removeBasicModelFile();
        $filePaths = array_map(function ($type) {
            return createPath(base_path(), 'api', $type, 'CategoryTableOfCategoryCategory') . '.php';
        }, $this->fileTypes);
        foreach ($filePaths as $filePath) {
            $this->assertFalse($this->fileSystem->exists($filePath));
        }
        $this->assertFalse($this->fileSystem->exists(createPath(base_path(), 'api', 'model', 'PivotMainTableOfCategoryCategory') . '.php'));
    }
}
