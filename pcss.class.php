<?php
class pcss {
  public $file;
  public $options = array(
    'spacing' => 1,
    'spacing_char' => "\t",
    'pcss_includes' => array()
  );
  
  private $lines = array();
  
  public function __construct($file,$args=array()) {
    $this->init($file,$args);
  }
  
  public function init($file,$args=array()) {
    $this->file = $file;
    $this->options = array_merge_recursive($this->options, $args);
    $this->setLines();
  }
  
  private function getRule($index) {
    $str = '';
    $v = $this->lines[$index];
    while(isset($v['parent'])) {
      $v = $this->lines[$v['parent']];
      $str = $v['line'] . ' ' . $str;
    }
    return $str;
  }
  
  private function getProperty($index) {
    $str = '';
    $v = $this->lines[$index];
    if(isset($v['first'])) {
      $str = $this->getRule($index) . "{\n";
    }
    $str .= str_repeat($this->options['indent_char'], $this->options['indent']) . $v['line'] . "\n";
    if(isset($v['last'])) {
      $str .= "}\n";
    }
    return $str;
  }
  
  private function setLines() {
    ob_start();
    if(is_array($this->options['pcss_includes'])) {      
      foreach($this->options['pcss_includes'] as $file) {
        require $file;
      }
    }
    require $this->file;
    $this->lines = explode(PHP_EOL,ob_get_clean());
    $this->lines = $this->buildLines();
  }
  
  private function buildLines() {
    $buckets = array();
    $l = array();
    $min_bucket = 0;
    foreach($this->lines as $num => $line) {
      $bucket = strspn($line," \t");
      $buckets[$bucket] = true;
      $line = trim($line);
      $l[$num] = array(
        'bucket' => $bucket,
        'line' => $line,
        'num' => $num,
        'type' => substr($line,-1) == ';' ? 'property' : 'rule'
      );  
      if($num == 0 || $bucket < $min_bucket) {
        $min_bucket = $bucket;
      }
    }

    $buckets_to_indexes = array_keys($buckets);
    sort($buckets_to_indexes);
    $buckets_to_indexes = array_flip($buckets_to_indexes);

    //so now let's build it out
    foreach($l as $num => $v) {
      $v['index'] = $buckets_to_indexes[$v['bucket']];
      if($num > 0 && $v['bucket'] > $min_bucket) {
        //let's find the parent    
        $index = $num;
        if($l[$index]['type'] == 'property') {
          if($l[$index-1]['type'] == 'rule') {
            $v['first'] = true;
          }
          if(!isset($l[$index+1]) || $l[$index+1]['type'] == 'rule') {
            $v['last'] = true;
          }
        }
        while($index > 0) {
          $index--;
          if($l[$index]['bucket'] < $v['bucket']) {
            $v['parent'] = $index;
            break;
          }
        }
      }
      $l[$num] = $v;
    }
    return $l;
  }
  
  public function cssHeader() {
    header('Content-Type: text/css');
  }
  
  public function getOutput() {
    $str = '';
    foreach($this->lines as $k => $v) {
      if($v['type'] == 'property') {
        $str .= $this->getProperty($k);
      }
    }
    return $str;
  }
  
  public function render() {
    $this->cssHeader();
    echo $this->getOutput();
  }
}