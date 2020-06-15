<?php

require_once '../bootstrap.php';

use Dnetix\Files\FileCopier;

//$src = 'https://scontent-mia1-1.xx.fbcdn.net/hphotos-xtf1/v/t1.0-9/12004092_10154180270486258_4936679115454937971_n.jpg?oh=cdef74ec0683f95fadb18bc980264a4f&oe=5756D874';
//$src = 'http://www.gravatar.com/avatar/' . md5('dnetix@gmail.com') . '?s=200';
$src = 'http://www.gravatar.com/avatar/' . md5('dnetix@gmail.com');

$fileCopier = FileCopier::create($src, './');

$copied = $fileCopier->copyIt();
if (!$copied) {
    print_r($fileCopier->error());
} else {
    echo '<h1>Imagen ' . $fileCopier->realName() . ' copiada</h1>';
    echo '<img src="./' . $fileCopier->realName() . '" />';
}
