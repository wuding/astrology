<?php
/**
 * 音乐艺术家
 */
namespace DbTable;

class MusicArtist extends \Astrology\Database
{
    public $db_name = 'audio';
    public $table_name = 'music_artist';
    public $primary_key = 'id';

    /**
     * 检测
     * @param  array  $arr 查询及设置数据
     * @return integer     条目ID或更新状态
     */
    public function exist($arr, $ignore = null)
    {
        $ignore = $ignore ? ['refresh_time'] : [];
        $primary_key = $this->primary_key;
        $time = time();

        $where = [
            'site' => $arr['site'],
            'artist' => $arr['artist'],
        ];
        $row = $this->sel($where, '*');

        if (!$row) {
            $data = [
                'status' => -1,
                'created' => $time,
                'updated' => $time,
            ];
            $data += $arr;
            return $this->insert($data);
        }

        /* 比较 */
        $diff = $this->array_diff_kv($arr, (array) $row, $ignore);
        if ($diff) {
            $data = [];
            foreach ($diff as $key => $value) {
                $data[$key] = $value[0];
            }
            $keys = array_keys($diff);
            # print_r([$diff, $data, $keys]);exit;
            $data['updated'] = $time;
            $data['status'] = -2;
            $data['note'] = implode(',', $keys);
            return $result = $this->update($data, $row->{$primary_key});
        }

        return $row->{$primary_key};
    }

    /**
     * 获取多个条目
     * @param  array   $where  查询
     * @param  string  $column 列
     * @param  string  $order  排序
     * @param  integer $page   页码
     * @param  integer $limit  条数
     * @return array           结果条目
     */
    public function fetchAll($where = [], $column = '*', $order = 'detail_id ASC', $page = 1, $limit = 10)
    {
        $offset = $page * $limit - $limit;
        $option = [$order, "$offset,$limit"];
        return $all = $this->_select($where, $column, $option);
    }
}
