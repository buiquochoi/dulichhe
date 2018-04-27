<?php

/**
 * Created by PhpStorm.
 * User: PMTB
 * Date: 10/4/2017
 * Time: 5:01 PM
 */
class Paging
{
    public function page($page, $count_page){
        $paging = "";
        if($page == 1 || $count_page < 2){
            $paging .= '<li class="disabled"><a href="'.BASE_URL.'" class="demo-pli-arrow-left"></a></li>';
        }else{
            $paging .= '<li><a data-page="'.($page - 1).'" href="'.BASE_URL.'/?page='. ($page - 1) .'" class="demo-pli-arrow-left"></a></li>';
        }
        for($i=1; $i<= $count_page; $i++){
            if($i == $page){
                $paging .= '<li class="active"><a href="#">'.$i.'</a></li>';
            }else{
                $paging .= '<li><a data-page="'.$i.'" href="'.BASE_URL.'?page='.$i.'">'.$i.'</a></li>';
            }
        }
        if($page >= $count_page){
            $paging .= '<li class="disabled"><a href="#" class="demo-pli-arrow-right"></a></li>';
        }else{
            $paging .= '<li><a data-page="'.($page + 1).'" href="'.BASE_URL.'?page='. ($page + 1) .'" class="demo-pli-arrow-right"></a></li>';
        }

        return $paging;
    }
}