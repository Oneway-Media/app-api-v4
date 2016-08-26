<?php
define('LIMIT', 10);


class News {
	
	
	// Get Categories
	public static function category($id = null) {
        
        if ($id !== null) {
            $url = 'http://dev-news.oneway.vn/api/index.php/category-news/'.$id;    
        } else {
            $url = 'http://dev-news.oneway.vn/api/index.php/category-news';
        }
        
        return curlstream($url);
        
	}


	// Searching
    public static function search($keyword, $category = null) {
        
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];
        
        if( $category === null ) { // Search all            
            $raw = new WP_Query([
                'post_status' => 'publish',
                'post_type' => 'news',
                'posts_per_page' => LIMIT,
                's' => $keyword                
            ]);
        } else { //Search by category
            if( is_numeric($category) ) {
                // Using Term ID
                $raw = new WP_Query([
                    'post_status' => 'publish',
                    'post_type' => 'news',
                    'tax_query' => [
                        [
                            'taxonomy' => 'news_category',
                            'field'    => 'term_id',
                            'terms'    => $category,
                        ]
                    ],
                    'posts_per_page' => LIMIT,
                    's' => $keyword                
                ]);
            } else {
                // Using Term Slug
                $raw = new WP_Query([
                    'post_status' => 'publish',
                    'post_type' => 'news',
                    'tax_query' => [
                        [
                            'taxonomy' => 'news_category',
                            'field'    => 'slug',
                            'terms'    => $category,
                        ]
                    ],
                    'posts_per_page' => LIMIT,
                    's' => $keyword                
                ]);
            }
        }
            
        $pre = sanitize($raw->posts, $fields);
        
        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                $p['cover'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'large' )[0];
                $p['view'] = intval(get_post_meta( $p['id'], '_count-views_all', true ));
                $p['like'] = intval(get_post_meta( $p['id'], 'oneway_like', true ));
                $p['share'] = intval(get_post_meta( $p['id'], 'oneway_share', true ));
                $p['comment'] = intval(wp_count_comments($p['id'])->approved);
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }
        
    }


	// Get list of News.
    public static function listNews($from,$limit = null, $sort = null) {

       if ($from == null) {
            $url = 'http://dev-news.oneway.vn/api/index.php/news';
        } else {
            if ($sort !== null && $limit !== null) {
                $url = 'http://dev-news.oneway.vn/api/index.php/news/'.$from.'/'.$limit.'/'.$sort;    
            } elseif ($limit !== null && $sort == null) {
                $url = 'http://dev-news.oneway.vn/api/index.php/news/'.$from.'/'.$limit;    
            } else {
                $url = 'http://dev-news.oneway.vn/api/index.php/news/'.$from;    
            }
            
        }
        
        return curlstream($url);

    }


    //Get list of news belong to specific category
    public static function listNewsCate($id,$from = null,$limit = null, $sort=null) {
        
        if ($from == null) {
            $url = "http://news.oneway.vn/api/index.php/news-category/".$id;
        } else {
            if ($from !== null && $limit == null && $sort == null) {
                $url = "http://news.oneway.vn/api/index.php/news-category/".$id.'/'.$from;
            } elseif ($from !== null && $limit !== null && $sort !== null) {
                $url = "http://news.oneway.vn/api/index.php/news-category/".$id.'/'.$from.'/'.$limit.'/'.$sort;
            } else {
                $url = "http://news.oneway.vn/api/index.php/news-category/".$id.'/'.$from.'/'.$limit;
            }
        }
        
        return curlstream($url);

    }

    //Get related news by anchor news.
    public static function listNewsRel($id,$from,$limit = null,$sort = null) {
        
        if ($id !== null && $from !== null) {
            $url = "http://news.oneway.vn/api/index.php/news-related/".$id.'/'.$from;
            if ($limit !== null && $sort !==null ) {
                $url = "http://news.oneway.vn/api/index.php/news-related/".$id.'/'.$from.'/'.$limit.'/'.$sort;
            }else {
                $url = "http://news.oneway.vn/api/index.php/news-related/".$id.'/'.$from.'/'.$limit;
            }
        }

        return curlstream($url);

    }


     //Get related news by anchor news.
    public static function newsItem($id,$fields = null) {
        
        // Switch $id to ID from slug
        if( !is_numeric($id) ) {
            // get posst from slug
            $post = get_page_by_path($id, OBJECT, 'news');
            
        } else {
            //get post from ID
            $post = get_post($id);
        }



        if ($fields == 'basic' || $fields === null) {
            $basic_fields = [
                'id' => 'ID',
                'slug' => 'post_name',
                'title' => 'post_title',
                'date' => 'post_date',
                'permalink' => '',
                'excerpt' => 'post_excerpt',
                'content' => 'post_content',
                'keyword' => '',
                'cover' => '',
                'view' => '',
                'like' => '',
                'share' => '',
                'thumbnail' => ''
            ];


            $pre = sanitize($post, $basic_fields);

            if(count($pre) > 0) {
                foreach($pre as $p) {
                    $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                    $p['permalink'] = get_permalink($p['id']);
                    $p['keyword'] = get_post_meta( $p['id'], '_yoast_wpseo_focuskw', true );
                    //preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $p['content'], $img);
                    //$p['cover'] = $img['1'];
                    $p['cover'] = wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'full' )[0];
                    $p['view'] = intval(get_post_meta( $p['id'], '_count-views_all', true ));
                    $p['like'] = intval(get_post_meta( $p['id'], 'oneway_like', true ));
                    $p['share'] = intval(get_post_meta( $p['id'], 'oneway_share', true ));
                    $p['comment'] = intval(wp_count_comments($p['id'])->approved);
                    
                    // Filter the content
                    $p['content'] = apply_filters( 'the_content', $p['content'] );
                    // $p['content'] = $p['content'];
                    
                    $output[] = $p;
                }

                return $output;
            } else {
                return [];
            }
        } else if ($fields == 'extra')  {
            $cmts = get_comments( 'post_id='.$id);
            $extra_fields = [
                'id' => 'comment_ID',
                'author_name' => 'comment_author',
                'author_email' => 'comment_author_email',
                'date' => 'comment_date',
                'content' => 'comment_content',
                'approved' => 'comment_approved',
                'parent_id' => 'comment_parent',
                'user_id' => 'user_id'
            ];

            $pre = sanitize($cmts, $extra_fields);

            if(count($pre) > 0) {
                return $pre;
            } else {
                return [];
            }

        } else if ($fields == 'full')  {

            $cmts = get_comments( 'post_id='.$id );
            $cmt_fields = [
                'id' => 'comment_ID',
                'author_name' => 'comment_author',
                'author_email' => 'comment_author_email',
                'date' => 'comment_date',
                'content' => 'comment_content',
                'approved' => 'comment_approved',
                'parent_id' => 'comment_parent',
                'user_id' => 'user_id'
            ];

            $cmts_pre = sanitize($cmts, $cmt_fields);

            $full_fields = [
                'id' => 'ID',
                'slug' => 'post_name',
                'title' => 'post_title',
                'date' => 'post_date',
                'permalink' => '',
                'excerpt' => 'post_excerpt',
                'content' => 'post_content',
                'keyword' => '',
                'cover' => '',
                'view' => '',
                'like' => '',
                'share' => '',
                'thumbnail' => '',
                'comments' => ''
            ];

            $full_pre = sanitize($post, $full_fields);

            if (count($full_pre) > 0) {
                foreach($full_pre as $p) {
                    $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                    $p['permalink'] = get_permalink($p['id']);
                    $p['keyword'] = get_post_meta( $p['id'], '_yoast_wpseo_focuskw', true );
                    preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $p['content'], $img);
                    $p['cover'] = $img['1'];
                    $p['view'] = get_post_meta( $p['id'], '_count-views_all', true );
                    $p['like'] = get_post_meta( $p['id'], 'oneway_like', true );
                    $p['share'] = get_post_meta( $p['id'], 'oneway_share', true );
                    $p['comments'] = $cmts_pre;
                    $output[] = $p;
                }

                return $output;

            } else {
                return [];
            }


        }


    }
	
    // Count all news.
    public static function countAll() {
        $allPosts = wp_count_posts('news');
        return $allPosts->publish;
    }

    // Count news by category.
    public static function countCate($id) {

        $taxonomy = "news_category"; // can be category, post_tag, or custom taxonomy name
         
        if( is_numeric($id) ) {
            // Using Term ID
            $term = get_term_by('id', $id, $taxonomy);
        } else {
            // Using Term Slug
            $term = get_term_by('slug', $id, $taxonomy);
        }
        // Fetch the count
        return $term->count;

    }

    // Get random items.
    public static function randomAll() {
        

        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];

        $arg = [
            'post_status' => 'publish',
            'post_type' => 'news',
            'posts_per_page' => LIMIT,  
            'orderby'   => 'rand'
        ];

        $raw = new WP_Query($arg);

        $pre = sanitize($raw->posts, $fields);

        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                $p['cover'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'large' )[0];
                $p['view'] = intval(get_post_meta( $p['id'], '_count-views_all', true ));
                $p['like'] = intval(get_post_meta( $p['id'], 'oneway_like', true ));
                $p['share'] = intval(get_post_meta( $p['id'], 'oneway_share', true ));
                $p['comment'] = intval(wp_count_comments($p['id'])->approved);
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }


    }

    // Get random items by category.
    public static function randomCate($id,$from, $limit = null) {
        $fields = [
            'id' => 'ID',
            'title' => 'post_title',
            'date' => 'post_date',
            'thumbnail' => ''
        ];

        if ($from > 0) {$from = $from - 1;} else if ($from <= 0) {$from = 0;};
        if ($limit == null) { $limit = LIMIT; };

        $offset = intval($from)*intval($limit);

        $arg = [
            'post_status' => 'publish',
            'post_type' => 'news',
            'offset' => $offset,
            'posts_per_page' => $limit,  
            'orderby'   => 'rand'
        ];


        if( is_numeric($id) ) {
            // By ID
            $arg['tax_query'] = [
                [
                    'taxonomy' => 'news_category',
                    'field'    => 'term_id',
                    'terms'    => $id,
                ]
            ];
        } else {
            // By Slug
            $arg['tax_query'] = [
                [
                    'taxonomy' => 'news_category',
                    'field'    => 'slug',
                    'terms'    => $id,
                ]
            ];
        }

        $raw = new WP_Query($arg);

        $pre = sanitize($raw->posts, $fields);

        if(count($pre) > 0) {
            foreach($pre as $p) {
                $p['thumbnail'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'thumbnail' )[0];
                $p['cover'] =  wp_get_attachment_image_src( get_post_thumbnail_id( $p['id'] ), 'large' )[0];
                $p['view'] = intval(get_post_meta( $p['id'], '_count-views_all', true ));
                $p['like'] = intval(get_post_meta( $p['id'], 'oneway_like', true ));
                $p['share'] = intval(get_post_meta( $p['id'], 'oneway_share', true ));
                $p['comment'] = intval(wp_count_comments($p['id'])->approved);
                $output[] = $p;
            }

            return $output;
        } else {
            return [];
        }
    }
	
}

?>  