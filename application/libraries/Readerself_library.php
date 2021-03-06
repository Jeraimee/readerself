<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Readerself_library {
	public function __construct($params = array()) {
		set_error_handler(array($this, 'error_handler'));
		$this->CI =& get_instance();
		$this->errors = array();
		if(function_exists('date_default_timezone_set')) {
			date_default_timezone_set('UTC');
		}
	}
	function error_handler($e_type, $e_message, $e_file, $e_line) {
		$e_type_values = array(1=>'E_ERROR', 2=>'E_WARNING', 4=>'E_PARSE', 8=>'E_NOTICE', 16=>'E_CORE_ERROR', 32=>'E_CORE_WARNING', 64=>'E_COMPILE_ERROR', 128=>'E_COMPILE_WARNING', 256=>'E_USER_ERROR', 512=>'E_USER_WARNING', 1024=>'E_USER_NOTICE', 2048=>'E_STRICT', 4096=>'E_RECOVERABLE_ERROR', 8192=>'E_DEPRECATED', 16384=>'E_USER_DEPRECATED', 30719=>'E_ALL');
		if(isset($e_type_values[$e_type]) == 1) {
			$e_type = $e_type_values[$e_type];
		}
		$value = $e_type.' | '.$e_message.' | '.$e_file.' | '.$e_line;
		$key = md5($value);
		$this->errors[$key] = $value;
	}
	function set_salt_password($mbr_password) {
		return sha1($mbr_password.$this->CI->config->item('salt_password'));
	}
	function set_template($template) {
		$this->template = $template;
	}
	function set_content_type($content_type) {
		$this->content_type = $content_type;
	}
	function set_charset($charset) {
		$this->charset = $charset;
	}
	function set_content($content) {
		$this->content = $content;
	}
	function get_debug() {
		if($this->content_type == 'application/json') {
			$debug = array();
			$debug['date'] = date('Y-m-d H:i:s');
			$debug['elapsed_time'] = $this->CI->benchmark->elapsed_time();
			if(function_exists('memory_get_peak_usage')) {
				$debug['memory_get_peak_usage'] = number_format(memory_get_peak_usage(), 0, '.', ' ');
			}
			if(function_exists('memory_get_usage')) {
				$debug['memory_get_usage'] = number_format(memory_get_usage(), 0, '.', ' ');
			}
			$debug['errors_count'] = count($this->errors);
			$debug['errors'] = array();
			foreach($this->errors as $error) {
				$debug['errors'][] = $error; 
			}
			$debug['queries_count'] = count($this->CI->db->queries);
			$debug['queries'] = array();
			$u = 0;
			foreach($this->CI->db->queries as $k => $query) {
				$query_time = number_format($this->CI->db->query_times[$k], 20, '.', '');
				$debug['queries'][$u] = array();
				$debug['queries'][$u]['query'] = $query;
				$debug['queries'][$u]['time'] = $query_time;
				$u++;
			}
		}
		if($this->content_type == 'text/plain' || $this->content_type == 'text/html') {
			$debug = "\n";
			if($this->content_type == 'text/html') {
				$debug .= '<!--'."\n";
			}
			$debug .= '##################################'."\n";
			$debug .= 'debug'."\n";
			$debug .= '##################################'."\n";
			$debug .= 'date: '.date('Y-m-d H:i:s')."\n";
			$debug .= 'elapsed_time: '.$this->CI->benchmark->elapsed_time()."\n";
			if(function_exists('memory_get_peak_usage')) {
				$debug .= 'memory_get_peak_usage: '.number_format(memory_get_peak_usage(), 0, '.', ' ')."\n";
			}
			if(function_exists('memory_get_usage')) {
				$debug .= 'memory_get_usage: '.number_format(memory_get_usage(), 0, '.', ' ')."\n";
			}
			$debug .= '##################################'."\n";
			$debug .= 'errors ('.count($this->errors).')'."\n";
			foreach($this->errors as $error) {
				$debug .= $error."\n";
			}
			$debug .= '##################################'."\n";
			$debug .= 'queries ('.count($this->CI->db->queries).')'."\n";
			foreach($this->CI->db->queries as $k => $query) {
				$debug .= '###'."\n";
				$query_time = number_format($this->CI->db->query_times[$k], 20, '.', '');
				$debug .= $query."\n";
				$debug .= $query_time."\n";
			}
			$debug .= '##################################'."\n";
			if($this->content_type == 'text/html') {
				$debug .= '-->'."\n\n";
			}
		}
		return $debug;
	}
	function build_filters($filters) {
		$flt = array();
		$flt[] = '1';
		foreach($filters as $k =>$v) {
			if(isset($_SESSION[$k]) == 0) {
				$_SESSION[$k] = '';
			}
			$value = '';
			if($this->CI->input->post($k) || isset($_POST[$k]) == 1) {
				$value = strval($this->CI->input->post($k));
				$this->CI->axipi_session->set_userdata($k, strval($this->CI->input->post($k)));
			} elseif($this->CI->axipi_session->userdata($k) != '') {
				$value = $this->CI->axipi_session->userdata($k);
			}
			if($value != '') {
				if($v[1] == 'compare_today') {
					if($value == 1) {
						$flt[] = $v[0].' <= '.$this->CI->db->escape(date('Y-m-d H:i:s'));
					}
					if($value == 0) {
						$flt[] = $v[0].' > '.$this->CI->db->escape(date('Y-m-d H:i:s'));
					}
				}
				if($v[1] == 'compare_field') {
					if($value == 1) {
						$flt[] = $v[0].' <= '.$v[2];
					}
					if($value == 0) {
						$flt[] = $v[0].' > '.$v[2];
					}
				}
				if($v[1] == 'null') {
					if($value == 1) {
						$flt[] = $v[0].' IS NULL';
					}
					if($value == 0) {
						$flt[] = $v[0].' IS NOT NULL';
					}
				}
				if($v[1] == 'notnull') {
					if($value == 1) {
						$flt[] = $v[0].' IS NOT NULL';
					}
					if($value == 0) {
						$flt[] = $v[0].' IS NULL';
					}
				}
				if($v[1] == 'inferior') {
					$flt[] = $v[0].' <= '.$this->CI->db->escape($value);
				}
				if($v[1] == 'superior') {
					$flt[] = $v[0].' >= '.$this->CI->db->escape($value);
				}
				if($v[1] == 'inferior_date') {
					$flt[] = $v[0].' <= '.$this->CI->db->escape($value.' 23:59:59');
				}
				if($v[1] == 'superior_date') {
					$flt[] = $v[0].' >= '.$this->CI->db->escape($value.' 00:00:00');
				}
				if($v[1] == 'equal') {
					if($value == -1) {
						$flt[] = $v[0].' IS NULL';
					} else {
						$flt[] = $v[0].' = '.$this->CI->db->escape($value);
					}
				}
				if($v[1] == 'like') {
					$flt[] = $v[0].' LIKE '.$this->CI->db->escape('%'.$value.'%');
				}
			}
		}
		return $flt;
	}
	function build_pagination($total, $per_page, $ref = 'default') {
		$this->CI->load->library('pagination');

		$config = array();
		$config['base_url'] = '?';
		$config['num_links'] = 5;
		$config['total_rows'] = $total;
		$config['per_page'] = $per_page;
		$config['page_query_string'] = TRUE;
		$config['use_page_numbers'] = TRUE;
		$config['query_string_segment'] = $ref.'_pg';
		$config['first_url'] = '?'.$config['query_string_segment'].'=1';
		$config['attributes'] = array('class' => 'mdl-color-text--'.$this->CI->config->item('material-design/colors/text/link'));

		$pages = ceil($total/$config['per_page']);

		$key = 'per_page_'.$config['query_string_segment'];
		if($this->CI->input->get($config['query_string_segment']) && is_numeric($this->CI->input->get($config['query_string_segment']))) {
			$page = $this->CI->input->get($config['query_string_segment']);
			$this->CI->axipi_session->set_userdata($key, $page);
		} else if($this->CI->axipi_session->userdata($key) && is_numeric($this->CI->axipi_session->userdata($key))) {
			$_GET[$config['query_string_segment']] = $this->CI->axipi_session->userdata($key);
		} else {
			$_GET[$config['query_string_segment']] = 0;
		}
		$start = ($this->CI->input->get($config['query_string_segment']) * $config['per_page']) - $config['per_page'];
		if($start < 0 || $this->CI->input->get($config['query_string_segment']) > $pages) {
			$start = 0;
			$_GET[$config['query_string_segment']] = 1;
		}

		if($pages == 1) {
			$position = $total;
		} else if($_GET[$config['query_string_segment']] == $pages && $pages != 0) {
			$position = ($start+1).'-'.$total.'/'.$total;
		} else if($pages != 0) {
			$position = ($start+1).'-'.($start+$config['per_page']).'/'.$total;
		} else {
			$position = $total;
		}

		$this->CI->pagination->initialize($config);
		return array('output'=>$this->CI->pagination->create_links(), 'start'=>$start, 'limit'=>$config['per_page'], 'position'=>$position);
	}
	function convert_author_title($auh_title) {
		$auh_id = false;
		$author_exists = $this->CI->db->query('SELECT auh.* FROM '.$this->CI->db->dbprefix('authors').' AS auh WHERE auh.auh_title = ? GROUP BY auh.auh_id', array($auh_title))->row();
		if($author_exists) {
			$auh_id = $author_exists->auh_id;
		} else {
			if($auh_title != '') {
				$this->CI->db->set('auh_title', $auh_title);
				$this->CI->db->set('auh_datecreated', date('Y-m-d H:i:s'));
				$this->CI->db->insert('authors');
				$auh_id = $this->CI->db->insert_id();
			}
		}
		return $auh_id;
	}
	function clean_authors($type, $value) {
		if($type == 'date') {
			$query = $this->CI->db->query('SELECT itm.itm_id, itm.itm_author FROM '.$this->CI->db->dbprefix('items').' AS itm WHERE itm.itm_author IS NOT NULL AND itm.auh_id IS NULL AND itm.itm_datecreated >= ?', array($value));
		}
		if($type == 'title') {
			$query = $this->CI->db->query('SELECT itm.itm_id, itm.itm_author FROM '.$this->CI->db->dbprefix('items').' AS itm WHERE itm.itm_author IS NOT NULL AND itm.auh_id IS NULL AND itm.itm_author = ?', array($value));
		}
		if($type == 'feed') {
			$query = $this->CI->db->query('SELECT itm.itm_id, itm.itm_author FROM '.$this->CI->db->dbprefix('items').' AS itm WHERE itm.itm_author IS NOT NULL AND itm.auh_id IS NULL AND itm.fed_id = ?', array($value));
		}
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$auh_id = false;
				$author_exists = $this->CI->db->query('SELECT auh.* FROM '.$this->CI->db->dbprefix('authors').' AS auh WHERE auh.auh_title = ? GROUP BY auh.auh_id', array($row->itm_author))->row();
				if($author_exists) {
					$auh_id = $author_exists->auh_id;
				} else {
					if($row->itm_author != '') {
						$this->CI->db->set('auh_title', $row->itm_author);
						$this->CI->db->set('auh_datecreated', date('Y-m-d H:i:s'));
						$this->CI->db->insert('authors');
						$auh_id = $this->CI->db->insert_id();
					}
				}
				if($auh_id) {
					$this->CI->db->set('auh_id', $auh_id);
					$this->CI->db->set('itm_author', '');
					$this->CI->db->where('itm_id', $row->itm_id);
					$this->CI->db->update('items');
				}
			}
		}
	}
	function convert_category_title($tag_title) {
		$tag_id = false;
		$tag_exists = $this->CI->db->query('SELECT tag.* FROM '.$this->CI->db->dbprefix('tags').' AS tag WHERE tag.tag_title = ? GROUP BY tag.tag_id', array($tag_title))->row();
		if($tag_exists) {
			$tag_id = $tag_exists->tag_id;
		} else {
			if($tag_title != '') {
				$this->CI->db->set('tag_title', $tag_title);
				$this->CI->db->set('tag_datecreated', date('Y-m-d H:i:s'));
				$this->CI->db->insert('tags');
				$tag_id = $this->CI->db->insert_id();
			}
		}
		return $tag_id;
	}
	function clean_categories($type, $value) {
		if($type == 'date') {
			$query = $this->CI->db->query('SELECT cat.itm_id, cat.cat_title FROM '.$this->CI->db->dbprefix('categories').' AS cat WHERE cat.cat_title IS NOT NULL AND cat.cat_datecreated >= ?', array($value));
		}
		if($type == 'title') {
			$query = $this->CI->db->query('SELECT cat.itm_id, cat.cat_title FROM '.$this->CI->db->dbprefix('categories').' AS cat WHERE cat.cat_title IS NOT NULL AND cat.cat_title = ?', array($value));
		}
		if($type == 'item') {
			$query = $this->CI->db->query('SELECT cat.itm_id, cat.cat_title FROM '.$this->CI->db->dbprefix('categories').' AS cat WHERE cat.cat_title IS NOT NULL AND cat.itm_id = ?', array($value));
		}
		if($type == 'feed') {
			$query = $this->CI->db->query('SELECT cat.itm_id, cat.cat_title FROM '.$this->CI->db->dbprefix('categories').' AS cat LEFT JOIN '.$this->CI->db->dbprefix('items').' AS itm ON itm.itm_id = cat.itm_id WHERE cat.cat_title IS NOT NULL AND itm.fed_id = ?', array($value));
		}
		if($query->num_rows() > 0) {
			foreach($query->result() as $row) {
				$tag_id = false;
				$tag_exists = $this->CI->db->query('SELECT tag.* FROM '.$this->CI->db->dbprefix('tags').' AS tag WHERE tag.tag_title = ? GROUP BY tag.tag_id', array($row->cat_title))->row();
				if($tag_exists) {
					$tag_id = $tag_exists->tag_id;
				} else {
					if($row->cat_title != '') {
						$this->CI->db->set('tag_title', $row->cat_title);
						$this->CI->db->set('tag_datecreated', date('Y-m-d H:i:s'));
						$this->CI->db->insert('tags');
						$tag_id = $this->CI->db->insert_id();
					}
				}
				if($tag_id) {
					$this->CI->db->set('tag_id', $tag_id);
					$this->CI->db->set('itm_id', $row->itm_id);
					$this->CI->db->set('tag_itm_datecreated', date('Y-m-d H:i:s'));
					$this->CI->db->insert('tags_items');

					$this->CI->db->where('cat_title', $row->cat_title);
					$this->CI->db->where('itm_id', $row->itm_id);
					$this->CI->db->delete('categories');
				}
			}
		}
	}
	function crawl_items($fed_id, $items) {
		foreach($items as $sp_item) {
			if(!$sp_item->get_link()) {
				continue;
			}
			$count = $this->CI->db->query('SELECT COUNT(DISTINCT(itm.itm_id)) AS count FROM '.$this->CI->db->dbprefix('items').' AS itm WHERE itm.itm_link = ? OR itm.itm_link = ?', array($sp_item->get_link(), str_replace('&amp;', '&', $sp_item->get_link())))->row()->count;
			if($count == 0) {

				$auh_id = false;
				if($author = $sp_item->get_author()) {
					$auh_id = $this->CI->readerself_library->convert_author_title($author->get_name());
				}

				$this->CI->db->set('fed_id', $fed_id);

				if($sp_item->get_title()) {
					$this->CI->db->set('itm_title', $sp_item->get_title());
				} else {
					$this->CI->db->set('itm_title', '-');
				}

				if($auh_id) {
					$this->CI->db->set('auh_id', $auh_id);
				}

				$this->CI->db->set('itm_link', str_replace('&amp;', '&', $sp_item->get_link()));

				if($sp_item->get_content()) {
					$this->CI->db->set('itm_content', $sp_item->get_content());
				} else {
					$this->CI->db->set('itm_content', '-');
				}

				if($sp_item->get_latitude() && $sp_item->get_longitude()) {
					$this->CI->db->set('itm_latitude', $sp_item->get_latitude());
					$this->CI->db->set('itm_longitude', $sp_item->get_longitude());
				}

				$sp_itm_date = $sp_item->get_gmdate('Y-m-d H:i:s');
				if($sp_itm_date) {
					$this->CI->db->set('itm_date', $sp_itm_date);
				} else {
					$this->CI->db->set('itm_date', date('Y-m-d H:i:s'));
				}

				$this->CI->db->set('itm_datecreated', date('Y-m-d H:i:s'));

				$this->CI->db->insert('items');

				$itm_id = $this->CI->db->insert_id();

				$enclosures = array();

				if($sp_item->get_iframes()) {
					foreach($sp_item->get_iframes() as $iframe) {
						if($iframe['src'] && $iframe['width'] && $iframe['height']) {
							if(stristr($iframe['src'], 'vimeo.com') || stristr($iframe['src'], 'youtube.com') || stristr($iframe['src'], 'dailymotion.com')) {
								if(!in_array($iframe['src'], $enclosures)) {
									$this->CI->db->set('itm_id', $itm_id);
									$this->CI->db->set('enr_link', $iframe['src']);
									if(stristr($iframe['src'], 'vimeo.com')) {
										$this->CI->db->set('enr_type', 'video/vimeo');
									}
									if(stristr($iframe['src'], 'youtube.com')) {
										$this->CI->db->set('enr_type', 'video/youtube');
									}
									if(stristr($iframe['src'], 'dailymotion.com')) {
										$this->CI->db->set('enr_type', 'video/dailymotion');
									}
									$this->CI->db->set('enr_width', $iframe['width']);
									$this->CI->db->set('enr_height', $iframe['height']);
									$this->CI->db->set('enr_datecreated', date('Y-m-d H:i:s'));
									$this->CI->db->insert('enclosures');
									$enclosures[] = $iframe['src'];
								}
							}
						}
					}
				}

				if($sp_item->get_categories()) {
					$categories_to_insert = array();
					foreach($sp_item->get_categories() as $category) {
						if($category->get_label()) {
							if(strstr($category->get_label(), ',')) {
								$categories = explode(',', $category->get_label());
								foreach($categories as $category_split) {
									$category_split = trim( strip_tags( html_entity_decode( $category_split ) ) );
									if($category_split != '') {
										$categories_to_insert[] = $category_split;
									}
								}
							} else {
								$category_split = trim( strip_tags( html_entity_decode( $category->get_label() ) ) );
								if($category_split != '') {
									$categories_to_insert[] = $category_split;
								}
							}
						}
					}
					foreach($categories_to_insert as $category) {
						$tag_id = $this->CI->readerself_library->convert_category_title($category);
						$this->CI->db->set('tag_id', $tag_id);
						$this->CI->db->set('itm_id', $itm_id);
						$this->CI->db->set('tag_itm_datecreated', date('Y-m-d H:i:s'));
						$this->CI->db->insert('tags_items');
					}
				}

				if($sp_item->get_enclosures()) {
					foreach($sp_item->get_enclosures() as $enclosure) {
						if($enclosure->get_link() && $enclosure->get_type()) {
							$link = $enclosure->get_link();
							if(substr($link, -2) == '?#') {
								$link = substr($link, 0, -2);
							}
							if(!in_array($link, $enclosures)) {
								$this->CI->db->set('itm_id', $itm_id);
								$this->CI->db->set('enr_link', $link);
								$this->CI->db->set('enr_type', $enclosure->get_type());
								$this->CI->db->set('enr_length', $enclosure->get_length());
								$this->CI->db->set('enr_width', $enclosure->get_width());
								$this->CI->db->set('enr_height', $enclosure->get_height());
								$this->CI->db->set('enr_datecreated', date('Y-m-d H:i:s'));
								$this->CI->db->insert('enclosures');
								$enclosures[] = $link;
							}
						}
					}
				}
			} else {
				break;
			}
			unset($sp_item);
		}
	}
	function crawl_items_instagram($fed_id, $items) {
		foreach($items as $sp_item) {
			if(isset($sp_item->link) == 0) {
				continue;
			}
			$count = $this->CI->db->query('SELECT COUNT(DISTINCT(itm.itm_id)) AS count FROM '.$this->CI->db->dbprefix('items').' AS itm WHERE itm.itm_link = ? OR itm.itm_link = ?', array($sp_item->link, str_replace('&amp;', '&', $sp_item->link)))->row()->count;
			if($count == 0) {
				$this->CI->db->set('fed_id', $fed_id);

				if(isset($sp_item->caption->text) == 1 && $sp_item->caption->text != '') {
					$this->CI->db->set('itm_title', $sp_item->caption->text);
				} else {
					$this->CI->db->set('itm_title', '-');
				}

				$this->CI->db->set('itm_link', str_replace('&amp;', '&', $sp_item->link));

				$this->CI->db->set('itm_content', '-');

				if(isset($sp_item->location)) {
					if(isset($sp_item->location->latitude) == 1 && isset($sp_item->location->longitude) == 1) {
						$this->CI->db->set('itm_latitude', $sp_item->location->latitude);
						$this->CI->db->set('itm_longitude', $sp_item->location->longitude);
					}
				}

				$sp_itm_date = $sp_item->created_time;
				if($sp_itm_date) {
					$this->CI->db->set('itm_date', date('Y-m-d H:i:s', $sp_itm_date));
				} else {
					$this->CI->db->set('itm_date', date('Y-m-d H:i:s'));
				}

				$this->CI->db->set('itm_datecreated', date('Y-m-d H:i:s'));

				$this->CI->db->insert('items');

				$itm_id = $this->CI->db->insert_id();

				if(isset($sp_item->videos) == 1) {
					$this->CI->db->set('itm_id', $itm_id);
					$this->CI->db->set('enr_link', $sp_item->videos->standard_resolution->url);
					$this->CI->db->set('enr_width', $sp_item->videos->standard_resolution->width);
					$this->CI->db->set('enr_height', $sp_item->videos->standard_resolution->height);
					$this->CI->db->set('enr_type', 'video/mp4');
					$this->CI->db->set('enr_datecreated', date('Y-m-d H:i:s'));
					$this->CI->db->insert('enclosures');
				} else if(isset($sp_item->images) == 1) {
					$this->CI->db->set('itm_id', $itm_id);
					$this->CI->db->set('enr_link', $sp_item->images->standard_resolution->url);
					$this->CI->db->set('enr_width', $sp_item->images->standard_resolution->width);
					$this->CI->db->set('enr_height', $sp_item->images->standard_resolution->height);
					$this->CI->db->set('enr_type', 'image/jpeg');
					$this->CI->db->set('enr_datecreated', date('Y-m-d H:i:s'));
					$this->CI->db->insert('enclosures');
				}

				if(isset($sp_item->tags)) {
					$sp_item->tags = array_unique($sp_item->tags);
					foreach($sp_item->tags as $tag) {
						$tag_id = $this->CI->readerself_library->convert_category_title($tag);
						$this->CI->db->set('tag_id', $tag_id);
						$this->CI->db->set('itm_id', $itm_id);
						$this->CI->db->set('tag_itm_datecreated', date('Y-m-d H:i:s'));
						$this->CI->db->insert('tags_items');
					}
				}
			} else {
				break;
			}
			unset($sp_item);
		}
	}
	function crawl_items_facebook($fed_id, $items) {
		foreach($items as $sp_item) {
			if(isset($sp_item['link']) == 0) {
				continue;
			}
			$count = $this->CI->db->query('SELECT COUNT(DISTINCT(itm.itm_id)) AS count FROM '.$this->CI->db->dbprefix('items').' AS itm WHERE itm.itm_link = ? OR itm.itm_link = ?', array($sp_item['link'], str_replace('&amp;', '&', $sp_item['link'])))->row()->count;
			if($count == 0) {
				$this->CI->db->set('fed_id', $fed_id);

				if(isset($sp_item['story'])) {
					$this->CI->db->set('itm_title', $sp_item['story']);
				} else {
					$this->CI->db->set('itm_title', '-');
				}

				$this->CI->db->set('itm_link', str_replace('&amp;', '&', $sp_item['link']));

				if(isset($sp_item['message']) == 1) {
					$this->CI->db->set('itm_content', nl2br($sp_item['message']));
				} else {
					$this->CI->db->set('itm_content', '-');
				}

				if(isset($sp_item['place'])) {
					if($sp_item['place']['location']['latitude'] && $sp_item['place']['location']['longitude']) {
						$this->CI->db->set('itm_latitude', $sp_item['place']['location']['latitude']);
						$this->CI->db->set('itm_longitude', $sp_item['place']['location']['longitude']);
					}
				}

				$sp_itm_date = $sp_item['created_time'];
				if($sp_itm_date) {
					$this->CI->db->set('itm_date', $sp_itm_date);
				} else {
					$this->CI->db->set('itm_date', date('Y-m-d H:i:s'));
				}

				$this->CI->db->set('itm_datecreated', date('Y-m-d H:i:s'));

				$this->CI->db->insert('items');

				$itm_id = $this->CI->db->insert_id();

				if(isset($sp_item['full_picture']) == 1) {
					$this->CI->db->set('itm_id', $itm_id);
					$this->CI->db->set('enr_link', $sp_item['full_picture']);
					$this->CI->db->set('enr_type', 'image/jpeg');
					$this->CI->db->set('enr_datecreated', date('Y-m-d H:i:s'));
					$this->CI->db->insert('enclosures');
				}
			} else {
				break;
			}
			unset($sp_item);
		}
	}
	function prepare_content($content) {
		//$content = strip_tags($content, '<dt><dd><dl><table><caption><tr><th><td><tbody><thead><h2><h3><h4><h5><h6><strong><em><code><pre><blockquote><p><ul><li><ol><br><del><a><img><figure><figcaption><cite><time><abbr>');

		//$content = str_replace(' src="', ' src="'.base_url().'proxy?file=', $content);

		preg_match_all('/<a[^>]+>/i', $content, $result);
		foreach($result[0] as $flr_a) {
			if(!preg_match('/(target)=("[^"]*")/i', $flr_a, $result)) {
				$content = str_replace($flr_a, str_replace('<a', '<a target="_blank"', $flr_a), $content);
			}
		}

		preg_match_all('/<img[^>]+>/i', $content, $result);
		foreach($result[0] as $flr_img) {
			$attribute_src = false;
			if(preg_match('/(src)=("[^"]*")/i', $flr_img, $result)) {
				$attribute_src = str_replace('"', '', $result[2]);
			}

			$attribute_width = false;
			if(preg_match('/(width)=("[^"]*")/i', $flr_img, $result)) {
				$attribute_width = str_replace('"', '', $result[2]);
			}

			$attribute_height = false;
			if(preg_match('/(height)=("[^"]*")/i', $flr_img, $result)) {
				$attribute_height = str_replace('"', '', $result[2]);
			}

			if($attribute_width == 1 || $attribute_height == 1 || stristr($attribute_src, 'feedsportal.com') || stristr($attribute_src, 'feedburner.com')) {
				$content = str_replace($flr_img, '', $content);
			} else {
				$flr_img_new = '<img src="'.$this->add_proxy($attribute_src).'"';
				if($attribute_width) {
					$flr_img_new .= ' width="'.$attribute_width.'"';
				}
				if($attribute_height) {
					$flr_img_new .= ' height="'.$attribute_height.'"';
				}
				$flr_img_new .= '>';
				$content = str_replace($flr_img, $flr_img_new, $content);
			}
		}
		return $content;
	}
	function timezone_datetime($datetime, $format = 'Y-m-d H:i:s') {
		return date($format, strtotime($datetime) + $this->CI->axipi_session->userdata('timezone') * 3600);
	}
	function add_proxy($url) {
		if($this->CI->config->item('proxy/enabled')) {
			if($this->CI->config->item('proxy/http_only') && substr($url, 0, 7) != 'http://') {
				return $url;
			} else {
				//return base_url().'medias/readerself_250x250.png';
				return base_url().'proxy/?file='.urlencode($url);
			}
		} else {
			return $url;
		}
	}
}
