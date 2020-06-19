<?php
namespace DbTable;

class _Abstract extends \Astrology\Database
{
    public function offset($offset = null, $site_id = null)
    {
        $sql = "SELECT * 
FROM $this->table_name 
WHERE `site` = '$site_id'
ORDER BY `id` 
LIMIT 1 
OFFSET $offset";

        return $row = self::$adapter->get($sql);
    }

    /**
     * 检测
     * @param  array  $arr 查询及设置数据
     * @return integer     条目ID或更新状态
     */
    public function exist($arr, $return = null, $variable = null)
    {
        $primary_key = $this->primary_key;
        $time = time();
        $variable = $variable ? : $this->exist_fields;
        $where = [];
        foreach ($variable as $key => $value) {
            $where[$value] = $arr[$value];
        }
        $row = $this->get($where, '*');

        if (!$row) {
            $data = [
                'status' => -1,
                'created' => $time,
                'updated' => $time,
            ];
            $arr += $data;
            return $this->insert($arr);
        }

        $diff = $this->array_diff_kv($arr, (array) $row);
        if ($diff) {
            $data = [];
            foreach ($diff as $key => $value) {
                $data[$key] = $value[0];
            }
            $keys = array_keys($diff);
            $data['updated'] = $data['compared'] = $time;
            $data['compares'] = $row->compares + 1;
            $data['diff'] = implode(',', $keys);
            return $this->update($data, $row->$primary_key);
        }
        if ($return) {
            return $row;
        }
        return $row->$primary_key;
    }
}
