<?php
namespace DbTable;

class MusicSiteLyric extends DbMusic
{
    public $table_name = 'site_lyric';
    public $exist_fields = ['site', 'song', 'version', 'size', 'type'];
}
