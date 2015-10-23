<?php
/**
 * A field type for YouTube videos
 *
 * version 2.0
 *
 * @package default
 * @author Drew McLellan
 */
class PerchFieldType_youtube extends PerchAPI_FieldType
{
    private $api_url = 'https://www.googleapis.com/youtube/v3/videos';

    private $player_opts = array('autohide', 'autoplay', 'cc_load_policy', 'color', 'controls', 'disablekb', 'enablejsapi', 'end', 'fs', 'hl', 'iv_load_policy', 'list', 'listType', 'loop', 'modestbranding', 'origin', 'playerapiid', 'playlist', 'playsinline', 'rel', 'showinfo', 'start', 'theme');

    /**
     * Output the form fields for the edit page
     *
     * @param array $details
     * @return void
     * @author Drew McLellan
     */
    public function render_inputs($details=array())
    {
        if (!defined('PERCH_YOUTUBE_API_KEY')) {
            return '<p class="help">You must configure a YouTube API key.</p>';
        }

        $id = $this->Tag->input_id();
        $val = '';

        if (isset($details[$id]) && $details[$id]!='') {
            $json = $details[$id];
            $val  = $json['path'];
        }else{
            $json = array();
        }

        $s = $this->Form->text($this->Tag->input_id(), $val);

        if (isset($json['youtubeID'])) {
            $ratio = 0.666666666;
            if (isset($json['height']) && $json['height']>0 && isset($json['width']) && $json['width']>0) {
                $ratio = $json['height'] / $json['width'];
            }
            $w = 320;
            $h = $w*$ratio;
            $s.= '<div class="preview"><iframe width="'.$w.'" height="'.$h.'" src="http://www.youtube.com/embed/'.$json['youtubeID'].'" frameborder="0"></iframe></div>';
        }

        return $s;
    }

    /**
     * Read in the form input, prepare data for storage in the database.
     *
     * @param string $post
     * @param object $Item
     * @return void
     * @author Drew McLellan
     */
    public function get_raw($post=false, $Item=false)
    {
        $store = array();

        $id = $this->Tag->id();

        if ($post===false) {
            $post = $_POST;
        }

        if (isset($post[$id])) {
            $this->raw_item = trim($post[$id]);
            $url = $this->raw_item;
        }

		if ($url) {
	        $store['path'] = $url;
	        $store['youtubeID'] = $this->get_id($url);

	        $details = $this->get_details($store['youtubeID']);

	        if ($details) {
	            $store = array_merge($store, $details);
	            $store['_title'] = $store['title'];
	        }
		}

        return $store;
    }

    /**
     * Take the raw data input and return process values for templating
     *
     * @param string $raw
     * @return void
     * @author Drew McLellan
     */
    public function get_processed($raw=false)
    {
        if (is_array($raw)) {

            $item = $raw;

            if ($this->Tag->output() && $this->Tag->output()!='path') {
                switch($this->Tag->output()) {

                    case 'id':
                        return isset($item['youtubeID']) ? $item['youtubeID'] : false;
                        break;

                    case 'embed':
                        $w = $item['width'];
                        $h = $item['height'];

                        if ($this->Tag->width()) {
                            $w = $this->Tag->width();
                            $h = '';

                            if ($this->Tag->height()) {
                                $h = $this->Tag->height();
                            }else{
                                $ratio = $item['height'] / $item['width'];
                                $h = $w*$ratio;
                            }
                        }

                        $query = array();

                        foreach($this->player_opts as $opt) {
                            if ($this->Tag->is_set($opt)) {
                                $query[$opt] = $this->Tag->attributes[$opt];
                            }
                        }

                        $q = http_build_query($query);

                        $this->processed_output_is_markup = true;

                        return '<iframe width="'.$w.'" height="'.$h.'" src="https://www.youtube.com/embed/'.$item['youtubeID'].'?'.$q.'" frameborder="0" allowfullscreen></iframe>';
                        break;

                    default:
                        if (isset($item[$this->Tag->output()])) {
                            return $item[$this->Tag->output()];
                        }
                        break;
                }
            }

            return $item['path'];
        }
        return $raw;
    }

    /**
     * Get the value to be used for searching
     *
     * @param string $raw
     * @return void
     * @author Drew McLellan
     */
    public function get_search_text($raw=false)
    {
        if ($raw===false) $raw = $this->get_raw();
        if (!PerchUtil::count($raw)) return false;

        if (isset($raw['title'])) return $raw['title'];

		return false;
    }


	/**
	 * Finds the YouTube video ID from a YouTube URL
	 *
	 * @param string $url YouTube video page URL
	 * @return string YouTube ID
	 * @author Drew McLellan
	 */
    private function get_id($url)
	{
        $parsed_url = parse_url($url);

        if ($parsed_url) {
            $query  = (isset($parsed_url['query']) ? $parsed_url['query'] : false);
            $host   = (isset($parsed_url['host']) ? $parsed_url['host'] : false);

            switch($host) {

                case 'youtu.be':
                    $parts = explode('/', trim($parsed_url['path'], '/'));
                    return $parts[0];
                    break;

                default:
                    if ($query) {
                        parse_str($query, $parts);
                        if (is_array($parts) && isset($parts['v'])) {
                            return $parts['v'];
                        }
                    }
                    break;
            }
        }

		return false;
	}


	/**
	 * Get information about the video with the given ID.
	 *
	 * @param string $videoID A YouTube video ID
	 * @return array Assoc array of video details
	 * @author Drew McLellan
	 */
	private function get_details($videoID)
	{
		$url = $this->api_url . $videoID;
        $url = $this->api_url .'?'. http_build_query(array(
                    'id'   => $videoID,
                    'part' => 'snippet,contentDetails,statistics,player',
                    'key'  => PERCH_YOUTUBE_API_KEY,
                ));

		$ch 	= curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$json_string = curl_exec($ch);
		curl_close($ch);

		if ($json_string) {
            PerchUtil::debug($json_string, 'notice');
            $json = PerchUtil::json_safe_decode($json_string, true);

            if (isset($json['items']) && isset($json['items'][0])) {
                $item = $json['items'][0];

               $out = array();
               $out['title']         = $item['snippet']['title'];
               $out['description']   = $item['snippet']['description'];
               $out['user_name']     = $item['snippet']['channelTitle'];
               $out['user_url']      = '';
               $out['url']           = 'https://www.youtube.com/watch?v='.$videoID;
               $out['date']          = date('Y-m-d H:i:s', strtotime($item['snippet']['publishedAt']));

               $out['thumb']         = $item['snippet']['thumbnails']['default']['url'];
               $out['thumb_w']       = $item['snippet']['thumbnails']['default']['width'];
               $out['thumb_h']       = $item['snippet']['thumbnails']['default']['height'];

               $thumb_key = 'high';
               if (isset($item['snippet']['thumbnails']['maxres'])) {
                    $thumb_key = 'maxres';
               }

               $out['width']         = $item['snippet']['thumbnails'][$thumb_key]['width'];
               $out['height']        = $item['snippet']['thumbnails'][$thumb_key]['height'];
               $out['player']        = $item['player']['embedHtml'];

               $out['rating']        = $item['statistics']['likeCount'];
               $out['likes']         = $item['statistics']['likeCount'];
               $out['dislikes']      = $item['statistics']['dislikeCount'];
               $out['favorites']     = $item['statistics']['favoriteCount'];
               $out['comments']      = $item['statistics']['commentCount'];
               $out['views']         = $item['statistics']['viewCount'];


                $interval = new DateInterval($item['contentDetails']['duration']);
                if ($interval) {
                    $out['duration'] = $interval->format('%H:%I:%S');
                }

                return $out;
            }
		}

		return false;
	}
}