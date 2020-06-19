<?php
namespace DbTable;

class MusicSiteAudio extends DbAudio
{
    public $table_name = 'music_site_audio';
    public $exist_fields = ['site', 'song', 'md5', 'size', 'br'];
}
