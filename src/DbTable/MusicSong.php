<?php
/**
 * 音乐歌曲
 */
namespace DbTable;

class MusicSong extends DbAudio
{
    public $table_name = 'music_site_song';

    /**
     * 检测
     * @param  array  $arr 查询及设置数据
     * @return integer     条目ID或更新状态
     */
    public function exist($arr, $ignore = null, $variable = null)
    {
        $ignore = $ignore ? ['refresh_time'] : [];
        $primary_key = $this->primary_key;
        $time = time();

        $where = [
            'site' => $arr['site'],
            'song' => $arr['song'],
        ];
        $row = $this->get($where, '*');

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
            $data['updated'] = $data['compared'] = $time;
            $data['compares'] = $row->compares + 1;
            $data['diff'] = implode(',', $keys);
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
