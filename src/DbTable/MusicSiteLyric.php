<?php
namespace DbTable;

class MusicSiteLyric extends DbAudio
{
    public $table_name = 'music_site_lyric';
    public $exist_fields = ['site', 'song', 'version', 'size', 'type'];
}
