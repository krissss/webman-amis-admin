<?php

namespace Kriss\WebmanAmisAdmin\Amis;

/**
 * @link https://aisuda.bce.baidu.com/amis/zh-CN/components/crud
 */
class Crud extends Component
{
    public function __construct()
    {
        $this->schema = [
            'type' => 'crud',
            'syncLocation' => false,
            'autoGenerateFilter' => true,
            'headerToolbar' => [
                10 => 'reload',
                20 => 'bulkActions',
                90 => [
                    'type' => 'columns-toggler',
                    'align' => 'right',
                    'draggable' => true,
                    'icon' => 'fas fa-cog',
                ],
            ],
            'footerToolbar' => [
                10 => 'switch-per-page',
                20 => 'pagination',
            ],
            'columns' => [],
        ];
    }

    public function withColumns(array $columns)
    {
        $quickEditEnable = isset($this->schema['quickSaveItemApi']);
        foreach ($columns as &$column) {
            if ($column instanceof Component) {
                $column = $column->toArray();
            }
            if ($quickEditEnable) {
                // 处理 quickEdit 为 true 时自动添加 saveImmediately = true
                if (isset($column['quickEdit']) && $column['quickEdit'] !== false) {
                    if (!is_array($column['quickEdit'])) {
                        $column['quickEdit'] = [];
                    }
                    $column['quickEdit']['saveImmediately'] = true;
                }
            }
        }
        unset($column);

        $this->schema['columns'] = $columns;
        return $this;
    }

    public function withCreate(string $api, array $form)
    {
        return $this->withHeaderToolbar(30, [
            'type' => 'button',
            'label' => '新增',
            'icon' => 'fa fa-plus',
            'actionType' => 'dialog',
            'level' => 'primary',
            'dialog' => [
                'title' => '新增',
                'body' => [
                    'type' => 'form',
                    'api' => $api,
                    'body' => $form,
                ],
            ],
        ]);
    }

    public function withHeaderToolbar(int $index, $schema)
    {
        $this->schema['headerToolbar'][$index] = $schema;
        return $this;
    }

    public function withFooterToolbar(int $index, $schema)
    {
        $this->schema['footerToolbar'][$index] = $schema;
        return $this;
    }

    public function toArray(): array
    {
        ksort($this->schema['headerToolbar']);
        $this->schema['headerToolbar'] = array_filter(array_values($this->schema['headerToolbar']));
        ksort($this->schema['footerToolbar']);
        $this->schema['footerToolbar'] = array_filter(array_values($this->schema['footerToolbar']));
        return parent::toArray();
    }
}