<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class AllowField extends FileCommon
{
    private function buildAllowText(array $fieldsData)
    {
        $allowHome = [];
        $allowRead = [];
        $allowSave = [];
        $allowUpdate = [];
        $allowTranslate = [];

        foreach ($fieldsData as $field) {
            // home
            if ($field['allowHome'] ?? false) {
                array_push($allowHome, $field['name']);
            }
            // read
            if ($field['allowRead'] ?? false) {
                array_push($allowRead, $field['name']);
            }
            // save
            if ($field['allowSave'] ?? false) {
                array_push($allowSave, $field['name']);
            }
            // update
            if ($field['allowUpdate'] ?? false) {
                array_push($allowUpdate, $field['name']);
            }
            // translate
            if ($field['allowTranslate'] ?? false) {
                array_push($allowTranslate, $field['name']);
            }
        }

        $allowHomeText = $allowHome ? '\'' . implode('\', \'', $allowHome) . '\'' : '';
        $allowReadText = $allowRead ? '\'' . implode('\', \'', $allowRead) . '\'' : '';
        $allowSaveText = $allowSave ? '\'' . implode('\', \'', $allowSave) . '\'' : '';
        $allowUpdateText = $allowUpdate ? '\'' . implode('\', \'', $allowUpdate) . '\'' : '';
        $allowTranslateText = $allowTranslate ? '\'' . implode('\', \'', $allowTranslate) . '\'' : '';

        return [$allowHomeText, $allowReadText, $allowSaveText, $allowUpdateText, $allowTranslateText];
    }

    public function createAllowFieldsFile(array $fieldsData)
    {
        list($allowHomeText, $allowReadText, $allowSaveText, $allowUpdateText, $allowTranslateText) = $this->buildAllowText($fieldsData);
        $replaceCondition = [
            '{{ allowHomeText }}' => $allowHomeText,
            '{{ allowReadText }}' => $allowReadText,
            '{{ allowSaveText }}' => $allowSaveText,
            '{{ allowUpdateText }}' => $allowUpdateText,
            '{{ allowTranslateText }}' => $allowTranslateText,
        ];
        $targetPath = createPath($this->rootPath, 'config', 'api', 'allowFields', $this->modelName) . '.php';
        $sourcePath = createPath($this->stubPath, 'AllowField', 'default') . '.stub';
        try {
            $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                return strtr($content, $replaceCondition);
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeAllowFieldsFile()
    {
        $targetPath = createPath($this->rootPath, 'config', 'api', 'allowFields', $this->modelName) . '.php';
        $this->fileSystem->remove($targetPath);
    }
}
