<?php
namespace DbTable;

class HlsM3u8 extends \Astrology\Database
{
    public $db_name = 'video';
    public $table_name = 'hls_m3u8';
    public $primary_key = 'm3u8_id';

    public $columns = [
        'row' => '*',
    ];
    public $where_array = null;

    /**
     * 根据名称查找
     *
     */
    public function findByName($name)
    {
        $field = is_numeric($name) ? 'm3u8_id' : 'name';
        $where = [
            $field => $name,
        ];
        return $row = $this->sel($where, '*');
    }
    /**
     * 根据条件查找所有
     *
     */
    public function fetchAll($where = [], $column = '*', $order = 'm3u8_id DESC')
    {
        $option = [$order, 30];
        return $all = $this->_select($where, $column, $option);
    }

    /**
     * 檢測列值是否存在，或插入
     */
    public function exist($arr, $lockTime = 'modified', $sort = null, $limit = 1, $offset = 0)
    {
        $primary_key = $this->primary_key;
        $time = time();

        /* 参数 */
        if (is_string($arr)) {
            $arr = [
                'url' => $arr,
            ];
        }

        /* 查询 */
        $where = array();
        if (null !== $this->where_array) {
            $where = $this->where_array;
            if (!array_key_exists('url', $arr)) {
                $arr['url'] = $where['url'];
            }

        } elseif (array_key_exists('url', $arr)) {
            $where = [
                'url' => $arr['url'],
            ];
        }

        $option = [$sort, $limit, $offset];
        # $row = $this->sel($where, '*');
        $row = $this->sel($where, $this->columns['row']);
        # print_r($row);print_r($this);exit;

        /* 新建 */
        if (!$row) {
            $data = [
                'created' => $time,
                $lockTime => $time,
            ];
            $data += $arr;
            return $this->insert($data);
        }
        $arr['updated'] = $time;
        $arr['updates'] = $row->updates + 1;

        /* 比较 */
        $diff = array_diff_kv($arr, (array) $row);
        if ($diff) {
            $data = [];
            foreach ($diff as $key => $value) {
                $data[$key] = $value[0];
            }
            $data[$lockTime] = $time;
            return $result = $this->update($data, $row->{$primary_key});
        }
        return $row->{$primary_key};
    }
}
