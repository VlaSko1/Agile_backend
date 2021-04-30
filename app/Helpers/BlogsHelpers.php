<?php


    /**
     * Modify object->created_at and object->updated_at (in format string): +3 hour
     * 
     * @param \App\Models\Blog  $blog
     * @return \App\Models\Blog  $blog - modified
     */
    function modifiedDateTimeString($item) {
        $created_at_date = new \DateTime($item->created_at);
        $created_at_date->modify('+3 hour');
        $created_at = $created_at_date->format('Y-m-d H:i:s');

        $updated_at_date = new \DateTime($item->updated_at);
        $updated_at_date->modify('+3 hour');
        $updated_at = $updated_at_date->format('Y-m-d H:i:s');

        $item->created_at = $created_at;
        $item->updated_at = $updated_at;
    }



    /**
     * Modify object->created_at and object->updated_at (in format DateTime): +3 hour
     * 
     * @param \App\Models\Blog  $blog
     * @return \App\Models\Blog  $blog - modified
     */

    function modifiedDateTime($item) {
        $item->created_at = date_modify($item->created_at, '+3 hour');
        $item->updated_at = date_modify($item->updated_at, '+3 hour');
    }


    /**
     * Returns $arrBlog 
     *
     * @param  $blog - object 
     * @param  $user_id - id user loggeg
     * @return $arrBlog - array objectes with 'likes' and 'userLike'
     */
    function getArrBlog($blog, $user_id) {

        // Выбираем id статей для сокращение запроса к таблице likes
        $blogIdArr = [];
        foreach($blog as $key=>$value) {
            array_push($blogIdArr, $value->id);
        }
        sort($blogIdArr, SORT_NUMERIC);

        $likes = \DB::table('likes')
             ->select(\DB::raw('count(*) as count, blog_id'))
             ->where('isLike', '=', 1)
             ->whereIn('blog_id', $blogIdArr)
             ->groupBy('blog_id')
             ->get();
        
        $userLike = \DB::table('likes')
             ->select('blog_id')
             ->where('isLike', '=', 1)
             ->where('user_id', $user_id)
             ->whereIn('blog_id', $blogIdArr)
             ->get();


        $arrBlog = [];
        foreach ($blog as $item) {
            modifiedDateTimeString($item);
            $item->likes = 0;
            $item->userLike = false;
            for ($i = 0; $i < count($likes); $i++) {
                if ($item->id === $likes[$i]->blog_id) {
                    $item->likes = $likes[$i]->count;
                    break;
                }
            }
            for ($j = 0; $j < count($userLike); $j++) {
                if ($item->id === $userLike[$j]->blog_id) {
                    $item->userLike = true;
                    break;
                }
            }
            $arrBlog[] = $item;
        }

        return $arrBlog;
    }