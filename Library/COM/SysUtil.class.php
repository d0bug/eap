<?php
import('ORG.Util.String');
class SysUtil {

	public static function getUserName($userName) {
		return String::removeXSS(trim($userName));
	}

	public static function getUserPass($userPass) {
		return trim($userPass);
	}

	public static function isEmail($email) {
		return preg_match('/\w+@(\w+.)+[a-z]{2,3}/', $email);
	}

	public static function isMobile($mobile) {
		return preg_match('/^1[358]\d{9}$/', $mobile);
	}

    public static function mobileMask($mobile) {
        return preg_replace('/^(\d{3})\d{4}(\d{4})$/', '\\1****\\2', $mobile);
    }

    public static function nameMask($name) {
        return '※' . mb_substr($name, 1, 100, 'UTF-8');
    }

	public static function safeString($str) {
		$str = trim(String::removeXSS(trim($str)));
        $str = str_replace(array('<x>','<x/>'), array('', ''), $str);
        return $str;
	}

	public static function safeSearch($str) {
		$str = self::safeString($str);
		$str = str_replace(array('%', '_','　'), array('', '', ' '), $str);
		return $str;
	}

	public static function uuid($uuid) {
		if($uuid) {
			return substr(self::safeString($uuid), 0, 36);
		}
		return trim(String::uuid(), '{}');
	}

	public static function getModuleActions($groupName, $moduleFile) {
		$moduleName = self::getClassNameByFile($moduleFile);
		$moduleName = preg_replace('/Action$/', '', $moduleName);
		@include_once(LIB_PATH . '/Action/' . $groupName . '/' . $groupName . 'CommAction.class.php');
		$module = A($groupName . '/' . $moduleName);
		$reflector = new ReflectionClass($module);
		$actions = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
		$actionArray = array();
		$exceptActions = C('EXCEPT_ACTIONS');
		foreach($actions as $action) {
			$actionName = $action->name;
			if($action->class == $moduleName . 'Action' && '_' != $actionName[0] && false == in_array($actionName, $exceptActions)) {
				$actionArray[] = $groupName . '-' . $moduleName . '-' . $actionName;
			}
		}
		return $actionArray;
	}

	private static function getClassNameByFile($fileName) {
		$fileName = basename($fileName);
		return str_replace('.class.php', '', $fileName);
	}


	public static function getRpcClient($moduleName) {

		require_once(VENDOR_PATH . '/phpRPC/phprpc_client.php');
		$apiUrl = C('API_URL_PREFIX') . '/' . $moduleName . '/index?key=' . C('API_KEY');
		$client = new PHPRPC_Client($apiUrl);
		$client->setKeyLength(1000);
		$client->setEncryptMode(3);
		$client->setCharset('UTF-8');
		$client->setTimeout(10);
		return $client;
	}

	public static function buildTree(&$dataList, $idField, $parentField, $rootValue) {
		$tree = array();
		foreach ($dataList as $key=>$data) {
			if($data[$parentField] == $rootValue) {
				$tree[$data[$idField]] = $data;
				unset($dataList[$key]);
			}
		}
		if($dataList) {
			foreach ($tree as $parentValue=>$data) {
				if($dataList) {
					$children = self::buildTree($dataList, $idField, $parentField, $parentValue);
					if($children) {
						$tree[$parentValue]['children'] = $children;
					}
				}
			}
		}
		return $tree;
	}

	public static function treeOptions($treeArray, $idField, $textField, $deep=0) {
		$options = array();
		$idx = 1;
		$cnt = sizeof($treeArray);
		foreach ($treeArray as $data) {
			$options[] = array('value'=>$data[$idField], 'caption'=>$data[$textField], 'deep'=>$deep, 'last'=>$idx == $cnt, 'hasChild'=>is_array($data['children']));
			if($data['children']) {
				$options = array_merge($options, self::treeOptions($data['children'], $idField, $textField, $deep + 1));
			}
			$idx  ++;
		}

		return $options;
	}

	public static function jsonEncode($data) {
		self::arrayRecursive($data);
		$data = json_encode($data);
		return str_replace("\r", '', str_replace("\n", '\n', urldecode($data)));
	}

	public static function jsonDecode($json) {
		return json_decode($json, true);
	}

	private static function arrayRecursive(&$data) {
		if(is_array($data)) {
			foreach ($data as $key=>$val) {
				self::arrayRecursive($data[$key]);
			}
		} else if (is_object($data)) {
			$data = (array) $data;
			self::arrayRecursive($data);
		} else {
			$data = urlencode($data);
		}
	}

	public static function sendFile($fileName, $contentType='application/octet-stream', $returnCfg=array()) {
		header("Pragma:public");
		header("Expires:0");
		header("Content-type:" . $contentType . ';charset=utf-8');
		header("Accept-Ranges:bytes");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		if ('' != $returnCfg['contents']) {
			ob_clean();
			$fileSize = strlen($returnCfg['contents']);
		} else if ('' != $returnCfg['filepath']){
			ob_clean();
			$fileSize = filesize($returnCfg['filepath']);
		}
		if($fileSize > 0)
		header("Accept-Length:".$fileSize);
		$ua = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/firefox/i', $ua)) {
			$fileName = str_replace('+', '%20', urlencode($fileName));
			$fileName = "utf8''" . $fileName;
			header("Content-Disposition:attachment; filename*=\"{$fileName}\"");
		} else if(preg_match('/msie/i', $ua)  || preg_match('/rv:/', $ua)){
			$fileName = str_replace('+', '%20', urlencode($fileName));
			header("Content-Disposition:attachment; filename=\"{$fileName}\"");
		} else {
			header("Content-Disposition:attachment; filename=\"{$fileName}\"");
		}
		if ('' != $returnCfg['contents']) {
			echo $returnCfg['contents'];
		} else if('' != $returnCfg['filepath']) {
			if(preg_match('/apache/i', $_SERVER['SERVER_SOFTWARE'])) {
				echo file_get_contents($returnCfg['filepath']);
			} else if (preg_match('/lighttpd/i', $_SERVER['SERVER_SOFTWARE'])) {
				header('X-LIGHTTPD-Send-file:' . $returnCfg['filepath']);
			} else if (preg_match('/nginx/i', $_SERVER['SERVER_SOFTWARE'])) {
				$nginxSendfileMaps = C('NGINX_SENDFILE_MAP');
				$filePath = $returnCfg['filepath'];
				foreach($nginxSendfileMaps as $map) {
					if(0 === strpos($filePath, $map[0])) {
						$filePath = str_replace($map[0], $map[1], $filePath);
						break;
					}
				}
				header('X-Accel-Redirect:' . $filePath);
			}
		}
		exit;
	}

	public static function datetime($date, $format) {
		if(false == is_numeric($date)) {
			$date = preg_replace('/:\d{3}[APM]+/i', '', $date);
			$timestamp = strtotime($date);
		} else {
			$timestamp = $date;
		}

		return date($format, $timestamp);

	}


    /**
     * CURL发送请求
     *
     * @param string $url
     * @param mixed $data
     * @param string $method
     * @param string $cookieFile
     * @param array $headers
     * @param int $connectTimeout
     * @param int $readTimeout
     */
    public static function curlRequest($url,$data='',$method='POST',$cookieFile='',$headers='',$connectTimeout = 30,$readTimeout = 30){
        $method = strtoupper($method);
        if(!function_exists('curl_init')) return self::socketRequest($url, $data, $method, $cookieFile, $connectTimeout);

        $option = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_TIMEOUT => $readTimeout
        );

        if($headers) $option[CURLOPT_HTTPHEADER] = $headers;

        if($cookieFile)
        {
            $option[CURLOPT_COOKIEJAR] = $cookieFile;
            $option[CURLOPT_COOKIEFILE] = $cookieFile;
        }

        if($data && strtolower($method) == 'post')
        {
            $option[CURLOPT_POST] = 1;
            $option[CURLOPT_POSTFIELDS] = $data;
        }

        if(stripos($url, 'https://') !== false)
        {
            $option[CURLOPT_SSL_VERIFYPEER] = false;
            $option[CURLOPT_SSL_VERIFYHOST] = false;
        }

        $ch = curl_init();
        curl_setopt_array($ch,$option);
        $response = curl_exec($ch);
        if(curl_errno($ch) > 0) throw_exception("CURL ERROR:$url ".curl_error($ch));
        curl_close($ch);
        return $response;
    }


    /**
     * socket发送请求
     *
     * @param string $url
     * @param string $post_string
     * @param string $method
     * @param int $connectTimeout
     * @param int $readTimeout
     * @return string
     */
    public static function socketRequest($url, $data, $method, $cookieFile, $connectTimeout) {
        $return = '';
        $matches = parse_url($url);
        !isset($matches['host']) && $matches['host'] = '';
        !isset($matches['path']) && $matches['path'] = '';
        !isset($matches['query']) && $matches['query'] = '';
        !isset($matches['port']) && $matches['port'] = '';
        $host = $matches['host'];
        $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
        $port = !empty($matches['port']) ? $matches['port'] : 80;

        $conf_arr = array(
            'limit'=>0,
            'post'=>$data,
            'cookie'=>$cookieFile,
            'ip'=>'',
            'timeout'=>$connectTimeout,
            'block'=>TRUE,
            );

        foreach ($conf_arr as $k=>$v) ${$k} = $v;
        if($post) {
            if(is_array($post))
            {
                $postBodyString = '';
                foreach ($post as $k => $v) $postBodyString .= "$k=" . urlencode($v) . "&";
                $post = rtrim($postBodyString, '&');
            }
            $out = "POST $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n";
            $out .= "Host: $host\r\n";
            $out .= 'Content-Length: '.strlen($post)."\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cache-Control: no-cache\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
            $out .= $post;
        } else {
            $out = "GET $path HTTP/1.0\r\n";
            $out .= "Accept: */*\r\n";
            //$out .= "Referer: $boardurl\r\n";
            $out .= "Accept-Language: zh-cn\r\n";
            $out .= "User-Agent: ".$_SERVER['HTTP_USER_AGENT']."\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Cookie: $cookie\r\n\r\n";
        }
        $fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
        if(!$fp) {
            return '';
        } else {
            stream_set_blocking($fp, $block);
            stream_set_timeout($fp, $timeout);
            @fwrite($fp, $out);
            $status = stream_get_meta_data($fp);
            if(!$status['timed_out']) {
                while (!feof($fp)) {
                    if(($header = @fgets($fp)) && ($header == "\r\n" ||  $header == "\n")) {
                        break;
                    }
                }

                $stop = false;
                while(!feof($fp) && !$stop) {
                    $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                    $return .= $data;
                    if($limit) {
                        $limit -= strlen($data);
                        $stop = $limit <= 0;
                    }
                }
            }
            @fclose($fp);
            return $return;
        }
    }

    public static function ajaxPager($recordCount, $currentPage, $pageSize) {
        return '';
    }
    
    public static function safeSearch_vip($str) {
		$str = self::safeString($str);
		$str = str_replace(array('%','　',"'"), array('', ' ',"\'"), $str);
		return $str;
	}
};
?>
