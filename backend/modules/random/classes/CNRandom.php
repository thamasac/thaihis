<?php

namespace backend\modules\random\classes;

class CNRandom {

    /**
     * 
     * @param type $speed  integer
     * @param type $group  array
     * @param type $block  array
     */
    public static function randomString($length = 6, $seed) {
        $str = "";
        $characters = array_merge(range('A', 'Z'), range('A', 'Z'));
        $num = array_merge(range('0', '9'), range('0', '9'));
        $func = function (){return mt_rand();};
        mt_srand($seed);
        $order = array_map($func, range(1, count($characters)));
        array_multisort($order, $characters);


        $order2 = array_map($func, range(1, count($num)));
        array_multisort($order2, $num);

        return $characters[0] . $characters[1] . $num[0];
    }

    public static function getRandomBlock($options, $status) {
        
        $seed = (int) $options['seed']; //seed
        $list_length = (int) $options['list_length']; //size
        $block_size = explode(',', $options['block_size']); //block size
        $group = explode(',', $options['treatment']); //group a , b         
        
        
        
        $blocksizeinit = $block_size; //array(4, 6, 8);

        $rs = 0;
        $a = 0;
        $b = 0;
        while ($rs <= $list_length) {
            $seed++;
            $bs = $blocksizeinit;
            
            mt_srand($seed);
            
            $func = function (){return mt_rand();};
            $order = array_map($func, range(1, count($blocksizeinit)));
            array_multisort($order, $bs);
            $roundarr[] = $bs[0];
            $rs = $rs + $bs[0];
        }

        
        
//        var_dump($roundarr);
//        echo $rs; //จำนวนรวม
//        exit;
//$roundarr=array(6,4,4,6,8,8,4,6,8);
        $html = "No., block ,identifier ,block size ,sequence within block ,treatment \n";
        $no = 1;
        
        //\appxq\sdii\utils\VarDumper::dump(count($roundarr));
        
        foreach ($roundarr as $round => $blocksize) {
            //$round=1;
            //$blocksize=6;
            //ต้องสร้าง arr ตามblocksize
            
            foreach ($group as $k => $v) {
                for ($i = 0; $i < $blocksize; $i++) {
                    $arr[] = $k;
                }
            } 
             
            //$arr = array(0,0,0,0,0,0,1,1,1,1,1,1);
            //var_dump($arr);
            $func = function (){return mt_rand();};
            mt_srand($seed++); 
            $order = array_map($func, range(1, count($arr)));
            array_multisort($order, $arr);
           
            
            for ($i = 0; $i < $blocksize; $i++) {
                $allrand[] = $arr[$i];
                $a = $a + ($arr[$i] == 0 ? 1 : 0);
                $b = $b + ($arr[$i] == 1 ? 1 : 0); 
                
                if ($status == 1) {
                    $block_name = self::getBlockName($group[$arr[$i]], $blocksize, $i, $group);
                    $html .= ($no++).", ".($round + 1) . ", {$blocksize}, " . ( $i + 1) . ", ". $block_name. ", " . CNRandom::randomString(3, $seed++) . "\n";
                    
                } else {
                    
                    $html .= ($no++).", ".($round + 1) . ", {$blocksize}, " . ($i + 1) . ", ". $group[$arr[$i]]. ", " . CNRandom::randomString(3, $seed++) . "|\n";
                }
            }
            unset($order);
            unset($arr);
        }
        echo $html;
        // echo "All random = ", count($allrand)," [$b,$b,$c]<br>";
    }
    private $name=[], $block_size = 0, $num=0, $group=[];
   
    private static function getBlockName($name,$block_size, $num, $group){
        return $name;
        array_push($this->name, $name);
        $this->num += $num;
        $this->block_size += $block_size;
    }

}
