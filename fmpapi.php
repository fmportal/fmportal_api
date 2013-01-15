<?php
class fmportal_api {
	public $content_items = array();
	public $tags = array();
	private $errors = array();

	public function add_content($type, $permalink, $title, $uid, $time_published, $full_text = '', $description = '', $tags = array())
	{
		if(!$permalink)
		{
			 $this->set_error('You must supply a permalink (URL) to where your content is located');
			 return FALSE;
		}
		if(!$title)
		{
			$this->set_error('You must supply a title for your content');
			return FALSE;
		}
		if(!$uid)
		{
			$this->set_error('You must supply some sort of Unique Identifier which can be used to reference this content item');
			return FALSE;
		}
		if(!$time_published)
		{
			$this->set_error('You must supply a date or timestamp of when your content was first published online');
			return FALSE;
		}

		if($tags && !is_array($tags))
		{
			$this->set_error('Your must supply tags as an array, an '.gettype($tags).' was supplied instead');
			return FALSE;
		}
		elseif($tags)
		{
			foreach($tags as $tag)
			{
				if(!is_string($tag))
				{
					$this->set_error('Tags supplied within an array must be strings an '.gettype($tag).' was supplied instead');
					return FALSE;
				}
			}
		}

		$this->content_items[] = array(
			'type'				=>	(string)$type,
			'permalink'			=>	(string)$permalink,
			'title'				=>	(string)$title,
			'uid'				=>	(string)$uid,
			'time_published'	=>	(string)$time_published,
			'full_text'			=>	(string)$full_text,
			'description'		=>	(string)$description,
			'tags'				=>	$tags
		);
	}

	public function get_output()
	{
		return json_encode(array(
			'content'	=>	$this->content_items
		));
	}

	public function send_output()
	{
		header("HTTP/1.0 200 OK");
		header('Content-type: application/json');
		echo $this->get_output();
		exit;
	}

	public function set_error($error)
	{
		$this->errors[] = $error;
	}

	public function get_last_error()
	{
		return end($this->errors);
	}

	public function get_errors()
	{
		return $this->errors;
	}
}

if(!function_exists('json_encode'))
{
	function json_encode( $array ){
	    if( !is_array( $array ) ){
	        return false;
	    }

	    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
	    if( $associative ){

	        $construct = array();
	        foreach( $array as $key => $value ){

	            // We first copy each key/value pair into a staging array,
	            // formatting each key and value properly as we go.

	            // Format the key:
	            if( is_numeric($key) ){
	                $key = "key_$key";
	            }
	            $key = "'".addslashes($key)."'";

	            // Format the value:
	            if( is_array( $value )){
	                $value = array_to_json( $value );
	            } else if( !is_numeric( $value ) || is_string( $value ) ){
	                $value = "'".addslashes($value)."'";
	            }

	            // Add to staging array:
	            $construct[] = "$key: $value";
	        }

	        // Then we collapse the staging array into the JSON form:
	        $result = "{ " . implode( ", ", $construct ) . " }";

	    } else { // If the array is a vector (not associative):

	        $construct = array();
	        foreach( $array as $value ){

	            // Format the value:
	            if( is_array( $value )){
	                $value = array_to_json( $value );
	            } else if( !is_numeric( $value ) || is_string( $value ) ){
	                $value = "'".addslashes($value)."'";
	            }

	            // Add to staging array:
	            $construct[] = $value;
	        }

	        // Then we collapse the staging array into the JSON form:
	        $result = "[ " . implode( ", ", $construct ) . " ]";
	    }

	    return $result;
	}
}
?>