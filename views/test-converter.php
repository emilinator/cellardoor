<?php
include_once 'classes/video.class.php';

$vid = new Video( "videos/sample1");
$vid->createPNG ("img", "test.png");
$vid->saveAsWEBM("videos", "test");

echo "done";