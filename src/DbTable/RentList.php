<?php
/**
 * 出租列表
 */
namespace DbTable;

class RentList extends \Astrology\Database
{
    // 数据库定义
    public $db_name = 'classified';
    public $table_name = 'rent_list';
    public $primary_key = 'list_id';

    /**
     * 获取最后更新时间
     */
    public function getLastUpdated()
    {
        $where = 'updated > 0';
        $row = $this->sel($where, 'updated', 'updated DESC');
        if ($row) {
            return $row->updated;
        }
        return false;
    }

    /**
     * 检测行
     * @param  array  $arr      新数据
     * @param  string $lockTime 锁定时间列名
     * @param  array  $ignore   忽略比较的列
     * @return integer          主键 ID 或数据库操作结果
     */
    public function exist($arr, $lockTime = 'updated', $ignore = [])
    {
        $primary_key = $this->primary_key;
        $time = time();
        $keys = array_merge(array_keys($arr), [$primary_key]);
        # print_r($keys);exit;

        /* 查询 */
        $where = [
            'detail_id' => $arr['detail_id'],
        ];
        $column = '*';
        $column = implode(',', $keys);
        $row = $this->sel($where, $column);

        /* 新建 */
        if (!$row) {
            $data = [
                'created' => $time,
                $lockTime => $time,
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

            $data[$lockTime] = $time;
            return $result = $this->update($data, $row->{$primary_key});
        }
        
        return $row->{$primary_key};
    }

    /**
     * SQL 查询最后时间
     * @Author   WuBenli
     * @DateTime 2019-01-18T12:13:52+0800
     * @return   string                   条件语句
     */
    public function whereFinalTime()
    {
        $time = $this->getLastUpdated();
        if (!$time) {
            return '';
        }
        return $where = "updated > $time OR created > $time";
    }

    /**
     * 格式化数据
     * @param  object $row 原数据
     * @return array       格式化后的数据
     */
    public function formatData($row)
    {
        $Area = new RentingSiteArea;
        $Method = new RentalMethod;
        $Type = new HouseType;
        $Tag = new RentTag;

        // 地区
        $data = [];
        $data['city_id'] = $Area->city_id($row->city_name);            
        $area = $Area->area_id($row->area_id);
        $area_ids = [
            $data['city_id']
        ];
        foreach ($area as $k => $value) {
            $str = $k . '_id';
            $data[$str] = $value->area_id;
            $area_ids[] = $value->area_id;
        }

        // 租赁方式和租金、户型和面积
        $rental_method = $Method->exist(['title' => $row->rental_method]);
        $house_type = $Type->exist(['title' => $row->house_type]);
        $rental_price = trim($row->rental_price);
        $building_area = trim($row->building_area);
        if (preg_match('/(\d+)元\/月/', $rental_price, $matches)) {
            # [, $rental_price] = $matches; //5.4 是行不通的
            list(, $rental_price) = $matches;
        } else {
            $rental_price = -2;
        }
        if (preg_match('/(\d+)平米/', $building_area, $matches)) {
            list(, $building_area) = $matches;
        } else {
            $building_area = -2;
        }

        // 图片、标签
        $pic = trim($row->pic);
        $pic = str_replace('//static.soufunimg.com/common_m/m_public/images/loadingpic.jpg', '', $pic);
        $tags = $Tag->tag_ids($row->tags);

        // 检测数据
        $arr = [
            'detail_id' => $row->detail_id,
            'complex_id' => $row->complex_id,
            'rental_method' => $rental_method,
            'house_type' => $house_type,
            'rental_price' => $rental_price,
            'building_area' => $building_area,
            'title' => $row->title,
            'pic' => $pic,
            'tags' => $tags,
            'area_ids' => implode(',', array_unique($area_ids)),
        ];
        return $arr += $data;
    }

    /**
     * 复刻多行数据
     * @param  integer $page  页码
     * @param  integer $limit 条数
     * @return array          结果和页数
     */
    public function fork($page = 1, $limit = 10)
    {
        $offset =  $page * $limit - $limit;

        $Detail = new RentingSiteDetail;
        
        /* 取出列表 */
        $where = $this->whereFinalTime();
        $column = '*';
        $option = ['detail_id ASC', "$offset,$limit"];
        $all = $Detail->_select($where, $column, $option);
        $pageCount = $Detail->pageCount($where, $limit);
        # print_r($all);

        /* 检测列表 */
        $result = [];
        foreach ($all as $key => $row) {
            $arr = $this->formatData($row);
            $result[] = $this->exist($arr, 'synchronized');
        }

        return [$result, $pageCount];
    }
}
