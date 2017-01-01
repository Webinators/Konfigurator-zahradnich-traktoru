<?php

function bbreplace($text)
{

$text = preg_replace('/\[b](.*?)\[\/b]/si', '<b>$1</b>', $text);
$text = preg_replace('/\[u](.*?)\[\/u]/si', '<u>$1</u>', $text);
$text = preg_replace('/\[i](.*?)\[\/i]/si', '<i>$1</i>', $text);
$text = preg_replace('/\[s](.*?)\[\/s]/si', '<s>$1</s>', $text);
$text = preg_replace('/\[sp](.*?)\[\/sp]/si', '<sp>$1</sp>', $text);
$text = preg_replace('/\[sb](.*?)\[\/sb]/si', '<sb>$1</sb>', $text);

$text = preg_replace('/\[img width=(.*?),height=(.*?)](.*?)\[\/img]/si', '<img src="$3" width="$1" height=$2 />', $text);
$text = preg_replace('/\[img](.*?)\[\/img]/si', '<img src="$1"/>', $text);

$text = preg_replace('/\[video](.*?)\[\/video]/si', '<iframe width="560px" height="315px" src="http://www.yotbe.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $text);
$text = preg_replace('/\[url=(.*?)](.*?)\[\/url]/si', '<a href="$1" target="_blank">$2</a>', $text);
$text = preg_replace('/\[list](.*?)\[\/list]/si', '<ul>$1</ul>', $text);
$text = preg_replace('/\[list=1](.*?)\[\/list]/si', '<ol>$1</ol>', $text);

$text = preg_replace('/\[\*\](.*?)\[\/\*\]/si', '<li>$1</li>', $text);

$text = preg_replace('/\[color=(.*?)](.*?)\[\/color]/msi', '<font color="$1">$2</font>', $text);
$text = preg_replace('/\[font=(.*?)](.*?)\[\/font]/si', '<font family="$1">$2</font>', $text);

$text = preg_replace('/\[left](.*?)\[\/left]/si', '<p align="left">$1</p>', $text);
$text = preg_replace('/\[center](.*?)\[\/center]/si', '<p align="center">$1</p>', $text);
$text = preg_replace('/\[right](.*?)\[\/right]/si', '<p align="right">$1</p>', $text);
$text = preg_replace('/\[quote](.*?)\[\/quote]/si', '<blockquote>$1</blockquote>', $text);
$text = preg_replace('/\[code](.*?)\[\/code]/si', '<code>$1</code>', $text);

$text = preg_replace('/\[td](.*?)\[\/td]/i', '<td>$1</td>', $text);
$text = preg_replace('/\[tr](.*?)\[\/tr]/i', '<tr>$1</tr>', $text);

$text = preg_replace('/\[table](.*?)\[\/table]/si', '<table style="width: 100%; border-collapse: collapse;">$1</table>', $text);

$text = preg_replace('/\[size=50](.*?)\[\/size]/msi', '<h5>$1</h5>', $text);
$text = preg_replace('/\[size=85](.*?)\[\/size]/msi',  '<h4>$1</h4>', $text);
$text = preg_replace('/\[size=100](.*?)\[\/size]/msi', '<h3>$1</h3>', $text);
$text = preg_replace('/\[size=150](.*?)\[\/size]/msi', '<h2>$1</h2>', $text);
$text = preg_replace('/\[size=200](.*?)\[\/size]/msi', '<h1>$1</h1>', $text);

return $text;
}

?>