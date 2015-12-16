<?php

/**
 * Created by PhpStorm.
 * User: niceforbear
 * Date: 15/12/16
 * Time: 下午11:11
 */
class Ohyeah
{
    const DECIDE_CODE = 1023; # Just for debug;

    private $downloadUrl = 'default'; # The url page which is parsing and downloading.

    private $loop = 50; # For pretty loop printing

    private $charset = 'utf-8'; # charset of Chinese

    private $content = []; # content array, which include two section: its name and download link.

    public function __construct($downloadUrl = ''){
        try{
            if(empty($downloadUrl)){
                throw new Exception('The download url can\'t be empty.');
            }else{
                include('phpquery-master/phpQuery/phpQuery.php');
                $this->downloadUrl = $downloadUrl;
            }
        }catch (Exception $e){
            print $e->getMessage();
            exit;
        }
    }

    public function finish(){
        phpQuery::newDocumentFileHTML($this->downloadUrl, $this->charset);
        $nodeCount = pq("#list2")->count();
        $articleList = pq("#list2 tr");

        echo "Start\n";
        for($i = 0; $i < $this->loop; $i++)
            echo '=';
        echo "\n";

        $i = 0;
        foreach($articleList as $tr){
            $i++;
            if($i <= 1 ){ # Filter the first node which has no meaning.
                continue;
            }
            $className = $tr->nodeValue; # This class order and its name.
            $classNameCombine = explode(' ', $className);
            $j = 0;
            $this->content[$i-1]['chapterName'] = '';
            foreach($classNameCombine as $k => $v) { # Put all class order and its name into $this->content array.
                $v = trim($v);
                if (empty($v)) {
                    continue;
                }
                $j++;
                if($j == 1){
                    $this->content[$i-1]['chapterId'] = $v;
                }else{
                    $this->content[$i-1]['chapterName'] .= $v . " ";
                }
            }
            $child = pq("td", $tr)->html();
            $trueContent = explode(' ', $child);
            $linkString = '';
            foreach($trueContent as $k => $v){
                $v = trim($v);

                if(empty($v)) continue;

                if($v == "<a"){
                    $linkString = $v . ' ' . $trueContent[$k+1];
                    break;
                }
            }
            $urls = explode("\"", $linkString);
            $this->content[$i-1]['link'] = $urls[1];
//            $name = explode("<", $urls[2]);
//            $name = substr($name[0], 1);
//            $this->content[$i-1]['name'] = $name;
        }

        var_dump($this->content);

        for($i = 0; $i < $this->loop; $i++)
            echo '=';
        echo "\nFinished! \n";
    }

    private function curl($item){

    }
}

if(!empty($argv)){
    if($argv[1] == Ohyeah::DECIDE_CODE){
        $url = empty($argv[2]) ? 'http://open.163.com/special/opencourse/algorithms.html': $argv[2];
        $oy = new Ohyeah($url);
        $oy->finish();
    }else{
        echo "\nwrong\n";
    }
}else{
    echo "\nYou should type arg 1023 to get demo output. ^_^\n";
}