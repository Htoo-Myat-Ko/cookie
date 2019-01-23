<?php

  namespace dev1\tools;

  class tools
  {
    private $cookie_time = 3600;
    private function get_self_url()
    {
      return $_SERVER["REQUEST_URI"];
    }

    private function reset_cookie($cookie_key, $time, $url)
    {
      setcookie($cookie_key, false, $time, $url);
    }

    public function set_cookie_if_new($pagetitle, $base_url)
    {
      $self_url = $this->get_self_url();

      if(!$_COOKIE["links"])
        {
          $json_urls = $this->make_json($pagetitle, $self_url);
          setcookie("links", $json_urls, time()+$this->cookie_time, $base_url);
      } else {
          $json_urls = $this->make_json($pagetitle, $self_url, $_COOKIE["links"]);
          $return_json = array();
          foreach(json_decode($json_urls, true) as $each) {
            if(sizeof($return_json) < 3) {
              $return_json[] = $each;
            }
          }


          $this->reset_cookie("links", time()-$this->cookie_time, $base_url);

          setcookie("links", json_encode($return_json), time()+$this->cookie_time, $base_url);
        }
    }


    function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
    }

    function replace_string_between($tagOne, $tagTwo, $text, $replacement)
    {
      $startTagPos = strrpos($text, $tagOne);
      $endTagPos = strrpos($text, $tagTwo);
      $tagLength = $endTagPos - $startTagPos + 12;

      return $newText = substr_replace($text, $replacement,
          $startTagPos, $tagLength);
    }

    function loop_replace($string, $arr)
    {
      foreach ($arr as $key => $value_arr)
      {
        $trans_arr = array();
        foreach($value_arr as $key_value => $translate_value)
        {
          $trans_arr["{{" . $key_value . "}}"] = $translate_value;
        }
        $loop_data .= strtr($string, $trans_arr);
      }
      return $loop_data;
    }

    public function pull_template($path, $translate)
    {
        if($contents = file_get_contents($path))
        {
          $replace = $this->get_string_between($contents, "{foreach}", "{endforeach}");
          if($replace)
          {
          $replacement = $this->loop_replace($replace, $translate);
          $contents = $this->replace_string_between("{foreach}","{endforeach}", $contents, $replacement);
          }
          echo strtr($contents, $translate);
        }else {
        echo "<code>Template $path is missing.</code>";
        }
    }

    private function make_json($pagetitle, $needle, $haystack = array())
    {
      if(!empty($haystack))
      {
          $haystack = json_decode($haystack, true);
          if(count($haystack) > 3){
            array_shift($haystack);
          }

          foreach($haystack as $key => $sub_haystack)
          {
             if($sub_haystack["url"] == $needle)
             {
              unset($haystack[$key]);
              break;
             }
          }
          //
          // if(in_array($needle, $haystack))
          // {
          //   $key = array_search($needle, $haystack);
          //   unset($haystack[$key]);
          // }

          array_unshift($haystack, array("url" => $needle, "title" => $pagetitle));

      } else {

        array_unshift($haystack, array("url" => $needle, "title" => $pagetitle));
      }

      $json_format = json_encode($haystack);

      return $json_format;
    }

  }
