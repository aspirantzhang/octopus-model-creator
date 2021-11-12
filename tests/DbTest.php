<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use think\facade\Db as ThinkDb;
use aspirantzhang\octopusModelCreator\TestCase;

class DbTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ThinkDb::execute('DROP TABLE IF EXISTS `auth_rule`, `auth_rule_i18n`, `menu`, `menu_i18n`, `auth_group_rule`, `unit-test`, `unit-test_i18n`, `unit-test-2`, `unit-test-2_i18n`, `field-test`, `field-test_i18n`, `db-test`, `db-test_i18n`, `category_model`, `category_model_i18n`;');
        ThinkDb::execute(<<<END
CREATE TABLE `auth_rule` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
 `rule_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `type` tinyint(1) NOT NULL DEFAULT 1,
 `condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `create_time` datetime NOT NULL,
 `update_time` datetime NOT NULL,
 `delete_time` datetime DEFAULT NULL,
 `status` tinyint(1) NOT NULL DEFAULT 1,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `auth_rule_i18n` (
 `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `original_id` int(11) unsigned NOT NULL,
 `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `rule_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `translate_time` datetime DEFAULT NULL,
 PRIMARY KEY (`_id`),
 UNIQUE KEY `original_id` (`original_id`,`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `menu` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
 `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `hide_in_menu` tinyint(1) NOT NULL DEFAULT 0,
 `hide_children_in_menu` tinyint(1) NOT NULL DEFAULT 0,
 `flat_menu` tinyint(1) NOT NULL DEFAULT 0,
 `create_time` datetime NOT NULL,
 `update_time` datetime NOT NULL,
 `delete_time` datetime DEFAULT NULL,
 `status` tinyint(1) NOT NULL DEFAULT 1,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `menu_i18n` (
 `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `original_id` int(11) unsigned NOT NULL,
 `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `menu_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `translate_time` datetime DEFAULT NULL,
 PRIMARY KEY (`_id`),
 UNIQUE KEY `original_id` (`original_id`,`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `auth_group_rule` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `group_id` int(11) unsigned NOT NULL,
 `rule_id` int(11) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `group_rule_id` (`group_id`,`rule_id`),
 KEY `group_id` (`group_id`),
 KEY `rule_id` (`rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
    }

    public function testSingleMainCreate()
    {
        $config = [
            'name' => 'db-test',
            'title' => 'DB Test',
        ];
        $modelData = ModelCreator::db()->config($config)->create();
        $this->assertTrue(true);
        return $modelData;
    }

    /**
    * @depends testSingleMainCreate
    */
    public function testUpdate($modelData)
    {
        $fieldsData = [
            [
                "name" => "nickname",
                "title" => "Nick Name",
                "type" => "input",
                "settings" => [
                    "validate" => [
                        "require",
                        "length"
                    ],
                    "options" => [
                        "length" => [
                            "min" => 4,
                            "max" => 32
                        ]
                    ]
                ],
                "allowHome" => true,
                "allowRead" => true,
                "allowSave" => true,
                "allowUpdate" => true,
                "allowTranslate" => true
            ],
            [
                "name" => "gender",
                "title" => "Gender",
                "type" => "radio",
                "data" => [
                    [
                        "title" => "Mx",
                        "value" => "mx"
                    ],
                    [
                        "title" => "Mr",
                        "value" => "mr"
                    ],
                    [
                        "title" => "Ms",
                        "value" => "ms"
                    ]
                ],
                "settings" => [
                    "validate" => [
                        "require"
                    ]
                ],
                "allowHome" => true,
                "allowRead" => true,
                "allowSave" => true,
                "allowUpdate" => true
            ],
            [
                "name" => "married",
                "title" => "Married",
                "type" => "switch",
                "hideInColumn" => true,
                "data" => [
                    [
                        "title" => "Yes",
                        "value" => 1
                    ],
                    [
                        "title" => "No",
                        "value" => 0
                    ]
                ],
                "settings" => [
                    "display" => [
                        "listSorter"
                    ],
                    "validate" => [
                        "require"
                    ]
                ],
                "allowHome" => true,
                "allowRead" => true,
                "allowUpdate" => true,
                "allowSave" => true
            ]
        ];
        $reservedFields = [
            'id',
            'create_time',
            'update_time',
            'delete_time',
            'status',
            '_id',
            'original_id',
            'lang_code',
            'translate_time'
        ];
        $config = [
            'name' => 'db-test',
            'title' => 'DB Test',
        ];
        ModelCreator::db()->config($config)->update($fieldsData, ['gender', 'married'], $reservedFields, ['nickname']);
        $this->assertTrue(true);
        return $modelData;
    }

    /**
    * @depends testUpdate
    */
    public function testRemove($modelData)
    {
        $config = [
            'name' => 'db-test',
            'title' => 'DB Test',
        ];
        $modelData = ModelCreator::db()->config($config)->remove($modelData['topRuleId'], $modelData['topMenuId']);
        $this->assertTrue(true);
        return $modelData;
    }

    private function createCategoryTestingTable()
    {
            ThinkDb::execute('DROP TABLE IF EXISTS `model`, `model_i18n`, `category_model`, `category_model_i18n`, `pivot_main_table_category`;');
            ThinkDb::execute(<<<END
    CREATE TABLE `model` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `table_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `type` tinyint(4) unsigned NOT NULL DEFAULT 1,
    `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
    `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
    `rule_id` int(11) unsigned NOT NULL DEFAULT 0,
    `menu_id` int(11) unsigned NOT NULL DEFAULT 0,
    `create_time` datetime NOT NULL,
    `update_time` datetime NOT NULL,
    `delete_time` datetime DEFAULT NULL,
    `status` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `table_name` (`table_name`)
    ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    END
            );
            ThinkDb::execute(<<<END
    CREATE TABLE `model_i18n` (
     `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
     `original_id` int(11) unsigned NOT NULL,
     `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
     `model_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
     `translate_time` datetime DEFAULT NULL,
     PRIMARY KEY (`_id`),
     UNIQUE KEY `original_id` (`original_id`,`lang_code`)
    ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    END
            );

            $currentTime = date('Y-m-d H:i:s');
            ThinkDb::name('model')->insertAll([
                [
                    'id' => 1,
                    'table_name' => 'main_table',
                    'type' => 1,
                    'parent_id' => 0,
                    'data' => '',
                    'create_time' => $currentTime,
                    'update_time' => $currentTime,
                ],
            ]);

            ThinkDb::name('model_i18n')->insertAll([
                [
                    '_id' => 1,
                    'original_id' => 1,
                    'lang_code' => 'en-us',
                    'model_title' => 'Main Model',
                ],
            ]);
    }

    public function testCategoryModelDbCreate()
    {
        $this->createCategoryTestingTable();
        $config = [
            'name' => 'category_model',
            'title' => 'Category Model',
            'type' => 'category',
            'parentId' => 1,
        ];
        $modelData = ModelCreator::db()->config($config)->create();
        $this->assertTrue(true);
        return $config;
    }

    /**
    * @depends testCategoryModelDbCreate
    */
    public function testCategoryModelDbRemove($config)
    {
        $modelData = ModelCreator::db()->config($config)->remove(0, 0);
        $this->assertTrue(true);
        return $modelData;
    }
}
