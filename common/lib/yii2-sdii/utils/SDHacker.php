<?php
namespace appxq\sdii\utils;

use Yii;
/**
 * Description of SDHacker
 *
 * @author appxq
 */
class SDHacker {
    
    public static function stripSelectedJs($content){
        $cut = preg_match("%var ttt = \"(.*?)\"%", $content, $matches);
        if($cut){
            return $matches[1];
        }
        return false;
    }
    public static function stripSelectedAttrAll($tag, $attr, $content){
        $cut = preg_match("%(<$tag.*?>)%is", $content, $matches);
        if($cut){
            $cut_attr = preg_match_all("%$attr=\"(.*?)\"%", $matches[0], $matches_img);
            if($cut_attr){
                return $matches_img[1];
            }
        }
        return false;
    }
    
    public static function stripSelectedAttr($tag, $attr, $content){
        $cut = preg_match("%(<$tag.*?>)%is", $content, $matches);
        if($cut){
            $cut_attr = preg_match("%$attr=\"(.*?)\"%", $matches[0], $matches_img);
            if($cut_attr){
                return $matches_img[1];
            }
        }
        return false;
    }
    
    public static function stripSelectedTagsList($tag, $content, $property=''){
        $cut = preg_match_all("%<$tag$property.*?>(.*?)<\/$tag.*?>%is", $content, $matches);
        if($cut){
            return $matches;
        }
        
        return false;
    }
    
    public static function stripSelectedTags($tag, $content, $property=''){
        $cut = preg_match("%(<$tag$property.*?>)(.*?)(<\/$tag.*?>)%is", $content, $matches);
        if($cut){
            return $matches;
        }
        
        return false;
    }
    
    public static function stripSelectedTagsAll($tag, $content, $property='', $suffix='html'){
        $cut = preg_match("%(<$tag$property.*?>)(.*?)(<\/$suffix.*?>)%is", $content, $matches);
        if($cut){
            return $matches;
        }
        
        return false;
    }
    
    public static function getTitle($content){
        $matches = self::stripSelectedTags('h3', $content, ' class=\"panel-title\"');
        if($matches ){
            $title = '';
            if(isset($matches[2])){
                $title = $matches[2];
            } else {
                $title = $matches[0];
            }
            $cut = preg_match("%(<!--)(.*?)(-->)%is", $title, $search);
            if($cut){
                $title = trim(str_replace($search[0], '', $title));
            }
            
            return $title;
        }
        return '';
    }
    
    public static function getContent($content){
        $matches = self::stripSelectedTagsAll('div', $content, ' class=\"panel-body\"');
        if($matches && isset($matches[2])){
            $matches_content = self::stripSelectedTags('div', $matches[2], ' class=\"panel-body\"');
            if($matches_content && isset($matches_content[2])){
                return trim($matches_content[2]);
            }
        }
        return '';
    }
    
    public static function getImage($content, $path = '/web/img_anime/'){
        $matches = self::stripSelectedTagsAll('div', $content, ' class=\"panel-body\"');
        if($matches && isset($matches[2])){
            $matches_img = self::stripSelectedAttr('img', 'src', $matches[2]);
            
            $getFilename = explode('?', basename($matches_img));
            $filename = SDUtility::getMillisecTime().'_'.$getFilename[0];

            $copy_img = @copy($matches_img, Yii::$app->basePath . $path . $filename);
            if($copy_img){
                return $filename;
            }
            return $matches_img;
        }
        return '';
    }
    
    public static function getMovieId($url_movie){
        $idArr = explode('/', $url_movie);
        $count = count($idArr);
        
        return $idArr[$count-1];
    }
    
    public static function getMovieList($content){
        $matches = self::stripSelectedTagsAll('div', $content, ' class=\"panel-body\"');
        if($matches && isset($matches[2])){
            $matches_content = self::stripSelectedTagsAll('div', $matches[2], ' class=\"panel-body\"', 'script');
            if($matches_content && isset($matches_content[2])){
                $matches_list = self::stripSelectedTagsList('center', $matches_content[2]);
                if($matches_list && isset($matches_list[1][1])){
                    $matches_link = self::stripSelectedTagsList('a', $matches_list[1][1]);
                    if($matches_link && isset($matches_link[1][1])){
                        $movie_list = [];
                        foreach ($matches_link[0] as $key => $value) {
                            $matches_movie = self::stripSelectedAttr('a', 'href', $value);
                            if($matches_movie){
                                
                                $content_movie = file_get_contents($matches_movie);
                                $url_movie = self::stripSelectedJs($content_movie);
                                $movieId = self::getMovieId($url_movie);
                                
                                $content_embed = file_get_contents("https://animelovestory.com/fix?video=$movieId");
                                $matches_embed = self::stripSelectedAttr('iframe', 'src', $content_embed);
                                if($matches_embed){
                                    //embed url
                                    $movie_list[] = ['title'=>$matches_link[1][$key], 'url'=>$matches_embed];
                                }
                                
                            }
                            
                        }
                        
                        return $movie_list;
                    }
                }
            }
        }
        return '';
    }
}
