<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;
use think\facade\Lang;
use think\Exception;

class Rule extends DbCommon
{
    public function createRule(string $ruleTitle = null, int $parentId = 0, string $rulePath = '', string $lang = null): int
    {
        $ruleTitle = $ruleTitle ?? $this->modelTitle;
        $currentTime = date("Y-m-d H:i:s");
        $lang = $lang ?? Lang::getLangSet();
        try {
            $ruleId = Db::name('auth_rule')->insertGetId([
                'parent_id' => $parentId,
                'rule_path' => $rulePath,
                'create_time' => $currentTime,
                'update_time' => $currentTime,
            ]);
            Db::name('auth_rule_i18n')->insert([
                'original_id' => $ruleId,
                'lang_code' => $lang,
                'rule_title' => $ruleTitle,
                'translate_time' => $currentTime
            ]);
        } catch (Exception $e) {
            throw new Exception(__('failed to create rule', ['ruleTitle' => $ruleTitle]));
        }
        return (int)$ruleId;
    }

    public function createChildrenRules(int $parentRuleId, string $lang = null)
    {
        $lang = $lang ?? Lang::getLangSet();
        $childrenRules = [
            ['rule_title' => $this->modelTitle . __('rule_title_home'), 'rule_path' => 'api/' . $this->tableName . '/home'],
            ['rule_title' => $this->modelTitle . __('rule_title_add'), 'rule_path' => 'api/' . $this->tableName . '/add'],
            ['rule_title' => $this->modelTitle . __('rule_title_save'), 'rule_path' => 'api/' . $this->tableName . '/save'],
            ['rule_title' => $this->modelTitle . __('rule_title_read'), 'rule_path' => 'api/' . $this->tableName . '/read'],
            ['rule_title' => $this->modelTitle . __('rule_title_update'), 'rule_path' => 'api/' . $this->tableName . '/update'],
            ['rule_title' => $this->modelTitle . __('rule_title_delete'), 'rule_path' => 'api/' . $this->tableName . '/delete'],
            ['rule_title' => $this->modelTitle . __('rule_title_restore'), 'rule_path' => 'api/' . $this->tableName . '/restore'],
            ['rule_title' => $this->modelTitle . __('rule_title_i18n'), 'rule_path' => 'api/' . $this->tableName . '/i18n'],
            ['rule_title' => $this->modelTitle . __('rule_title_i18nUpdate'), 'rule_path' => 'api/' . $this->tableName . '/i18n_update'],
        ];
        $childrenIds = [];
        try {
            foreach ($childrenRules as $rule) {
                $childrenIds[] = $this->createRule($rule['rule_title'], $parentRuleId, $rule['rule_path'], $lang);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $childrenIds;
    }

    public function removeRules(int $id)
    {
        try {
            $allRulesData = Db::table('auth_rule')->where('status', 1)->select()->toArray();
            $allIds = array_merge([$id], searchDescendantValueAggregation('id', 'id', $id, arrayToTree($allRulesData)));
            Db::table('auth_rule')->whereIn('id', $allIds)->delete();
            Db::table('auth_rule_i18n')->whereIn('original_id', $allIds)->delete();
            Db::table('auth_group_rule')->whereIn('rule_id', $allIds)->delete();
        } catch (Exception $e) {
            throw new Exception(__('failed to remove rules'));
        }
    }
}