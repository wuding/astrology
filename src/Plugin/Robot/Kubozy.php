<?php
/**
 * 酷播资源网-最新影视资源大全
 * http://www.kubozy.net/ 
 *
 * http://api.kbzyapi.com/inc/api.php?ac=videolist&wd=&t=&h=0&ids=&pg=1
 * http://api.kbzyapi.com/inc/apikuyun.php?ac=videolist&wd=&t=&h=0&ids=&pg=1
 * http://api.kbzyapi.com/inc/apim3u8.php?ac=videolist&wd=&t=&h=0&ids=&pg=1
 */
namespace Plugin\Robot;

class Kubozy extends \Plugin\Robot
{
	# public $func_format = 'json';
	public $site_id = 41;
	
	/**
	 * 自定义初始化 
	 */
	public function _init()
	{
		$this->cache_dir = $this->cache_root . '/www.kubozy.net/' . date('Y-m') . '/';
		
		$this->urls = [
			'http://api.kbzyapi.com/inc/api.php?ac=videolist&wd=&t=&h=0&ids=&pg=%1',
		];
		
		$this->paths = [
			$this->cache_dir . '%1.xml',
		];
		
		$this->classes = [0,
			'电影', '连续剧', '综艺', '动漫', '动作片',
			'喜剧片', '爱情片', '科幻片', '恐怖片', '剧情片',
			'战争片', '国产剧', '港台剧', '福利', '欧美剧',
			'伦理片', '泰剧', '记录片', '韩剧', '日剧',
		];
	}
	
	/**
	 * 解析分类
	 */
	public function parseCategory()
	{
		return $this->parseCategoryArray();
	}
}
