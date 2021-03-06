<?php

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db as ThinkDb;

class TableCreatorTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ThinkDb::execute('DROP TABLE IF EXISTS `table-creator-main-extra`, `table-creator-i18n-extra`, `table-creator-custom`;');
    }

    public function testDefaultSqlIsEmpty()
    {
        $tableCreator = new TableCreator('unit-test');
        $this->assertEquals('', $tableCreator->getSql());
    }

    public function testBuildSqlOfTypeMain()
    {
        $tableCreator = new TableCreator('table-creator-main');
        $tableCreator->buildSql();
        $this->assertStringStartsWith("CREATE TABLE `table-creator-main` (\n        `id` int(11)", $tableCreator->getSql());
    }

    public function testCreateTableOfTypeMainWithExtra()
    {
        $tableCreator = (new TableCreator('table-creator-main-extra'))
            ->setExtraFields([
                "extra-field-1",
                "extra-field-2",
            ])
            ->setExtraIndexes([
                "extra-index-1",
                "extra-index-2",
            ]);
        $tableCreator->buildSql();
        $this->assertStringContainsString("CREATE TABLE `table-creator-main-extra` (\n        `id` int(11)", $tableCreator->getSql());
        $this->assertStringContainsString('extra-field-1', $tableCreator->getSql());
        $this->assertStringContainsString('extra-field-2', $tableCreator->getSql());
        $this->assertStringContainsString('extra-index-1', $tableCreator->getSql());
        $this->assertStringContainsString('extra-index-2', $tableCreator->getSql());
    }

    public function testCreateTableOfTypeI18nWithExtra()
    {
        $tableCreator = (new TableCreator('table-creator-i18n-extra', 'i18n'))
            ->setExtraFields([
                "extra-field-1",
                "extra-field-2",
            ])
            ->setExtraIndexes([
                "extra-index-1",
                "extra-index-2",
            ]);
        $tableCreator->buildSql();
        $this->assertStringContainsString("CREATE TABLE `table-creator-i18n-extra` (\n        `_id` int(11)", $tableCreator->getSql());
        $this->assertStringContainsString('extra-field-1', $tableCreator->getSql());
        $this->assertStringContainsString('extra-field-2', $tableCreator->getSql());
        $this->assertStringContainsString('extra-index-1', $tableCreator->getSql());
        $this->assertStringContainsString('extra-index-2', $tableCreator->getSql());
    }

    public function testRealCreationOfTypeMain()
    {
        (new TableCreator('table-creator-main-extra'))
        ->setExtraFields([
            "`number_field` int(11) NOT NULL DEFAULT 0",
            "`string_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''",
        ])
        ->setExtraIndexes([
            "KEY `single-key` (`number_field`)",
            "KEY `union-key` (`number_field`, `string_field`)",
        ])
        ->execute();
        $this->assertTrue(true);
    }

    public function testRealCreationOfTypeI18n()
    {
        (new TableCreator('table-creator-i18n-extra', 'i18n'))
        ->setExtraFields([
            "`number_field` int(11) NOT NULL DEFAULT 0",
            "`string_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''",
        ])
        ->setExtraIndexes([
            "KEY `single-key` (`number_field`)",
            "KEY `union-key` (`number_field`, `string_field`)",
        ])
        ->execute();
        $this->assertTrue(true);
    }

    public function testRealCreationOfTypeCustom()
    {
        (new TableCreator('table-creator-custom', 'custom'))
        ->setSql('CREATE TABLE `table-creator-custom` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `item_id` int(11) unsigned NOT NULL,
            `category_id` int(11) unsigned NOT NULL,
            PRIMARY KEY (`id`),
            KEY `item_id` (`item_id`),
            KEY `category_id` (`category_id`),
            KEY `item_category_id` (`item_id`,`category_id`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;')
        ->execute();
        $this->assertTrue(true);
    }
}
