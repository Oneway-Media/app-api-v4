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
        
        if ($category !== null ) {
            $url = "http://dev-news.oneway.vn/api/index.php/search-news/".$keyword.'/'.$category;
        } else {
            $url = "http://dev-news.oneway.vn/api/index.php/search-news/".$keyword;
        }
        return curlstream($url);
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
        

        if ($id == null || $from == null) {
            $url = 'http://dev-news.oneway.vn/api/index.php/news-category';
        } else {
            if ($sort !== null && $limit !== null) {
                $url = 'http://dev-news.oneway.vn/api/index.php/news-category/'.$id.'/'.$from.'/'.$limit.'/'.$sort;    
            } elseif ($limit !== null && $sort == null) {
                $url = 'http://dev-news.oneway.vn/api/index.php/news-category/'.$id.'/'.$from.'/'.$limit;    
            } else {
                $url = 'http://dev-news.oneway.vn/api/index.php/news-category/'.$id.'/'.$from;    

            }

        }
        
        return curlstream($url);

    }

    //Get related news by anchor news.
    public static function listNewsRel($id,$from,$limit = null,$sort = null) {
        

        if ($id == null || $from == null) {
            $url = 'http://dev-news.oneway.vn/api/index.php/news-related';
        } else {
            if ($sort !== null && $limit !== null) {
                $url = 'http://dev-news.oneway.vn/api/index.php/news-related/'.$id.'/'.$from.'/'.$limit.'/'.$sort;    
            } elseif ($limit !== null && $sort == null) {
                $url = 'http://dev-news.oneway.vn/api/index.php/news-related/'.$id.'/'.$from.'/'.$limit;    
            } else {
                $url = 'http://dev-news.oneway.vn/api/index.php/news-related/'.$id.'/'.$from;    

            }

        }

        
        return curlstream($url);

    }


     //Get related news by anchor news.
    public static function newsItem($id,$fields = null) {
        
        if ($id == null) {
            return  $url = "http://dev-news.oneway.vn/api/index.php/news-item";
        } else {
            if ($fields !== null){
            $url = "http://dev-news.oneway.vn/api/index.php/news-item/".$id.'/'.$fields;
            } else {
                $url = "http://dev-news.oneway.vn/api/index.php/news-item/".$id;
            }

        }

        return curlstream($url);
        
    }

	
    // Count all news.
    public static function countAll() {
        $url = "http://dev-news.oneway.vn/api/index.php/count-news";
        return curlstream($url);
    }

    // Count news by category.
    public static function countCate($id) {
        $url = "http://dev-news.oneway.vn/api/index.php/count-news/".$id;
        return curlstream($url);
    }

    // Get random items.
    public static function randomAll() {
        $url = "http://dev-news.oneway.vn/api/index.php/random-news";
        return curlstream($url);
    }

    // Get random items by category.
    public static function randomCate($id,$from, $limit = null) {
        
        if ($from == null) {
            $url = "http://dev-news.oneway.vn/api/index.php/random-news/".$id;
        } else {
            if ($limit !== null) {
                $url = "http://dev-news.oneway.vn/api/index.php/random-news/".$id.'/'.$from.'/'.$limit;
            } else {
                $url = "http://dev-news.oneway.vn/api/index.php/random-news/".$id.'/'.$from;
            }
        }
        return curlstream($url);
    }
	
}

?>  