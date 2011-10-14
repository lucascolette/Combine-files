<?php
/*
 * Combine js and css files with cache system
 * Based on http://www.chrisrenner.com/2011/02/minify-and-compress-all-your-javascript-files-into-one-on-the-fly/
 *
 * @version 1.0
 * @author Lucas Colette <eu@colet.me>
 * 
 */

class CombineFiles {
  
  public function __construct () {
    
  }

  /*
   * Concatenate and minify multiple files, js or css,
   * defined on param $type
   *
   * @uses CombineFiles::getFileName() to get the hash of the file
   * @uses CombineFiles::compareFiles() to verify if the cache file already exists
   *
   * @param $filesDir string Source dir of original files
   * @param $cacheDir string
   * @param $files array List of files to be combined
   */

  public function Fetch ( $filesDir, $cacheDir, $files, $type ) {
    
    $cacheFile    = self::getFileName($files, $type);
    $hasCacheFile = self::compareFiles($filesDir, $cacheDir, $files, $type, $cacheFile);

    /* Verify the cache file */
    if ( !$hasCacheFile ) {
      
      $content  = null;

      foreach ( $files as $script ) {
        $extensionFile  = ( $type == 'js' ) ? '.js' : '.css';
        $content  .=  file_get_contents($filesDir . '/' . $script . $extensionFile);
      }

      $minify = ($type == 'js') ? JSMin::minify($content) : self::minifyCSS($content);

      $openFile = fopen($cacheDir . '/' . $cacheFile, "w");
      fwrite($openFile, $minify);
      fclose($openFile);

    }

    return $cacheDir . '/' . $cacheFile;

  }

  /*
   * Get the hash of the cache file
   * 
   * @param $files string 
   * @param $type string css or js
   * @return string
   */
  
  public function getFileName ( $files, $type ) {
    $hashFile   = md5( implode('_', $files) ).'.'.$type;
    return $hashFile;
  }

  /*
   * Compare the modified date of the source files
   * against the hash file if it exists and return true if the hash
   * file is newer and
   * return false if its older or if hash file doesn't exist
   *
   * @param $filesDir string
   * @param $cacheDir string
   * @param $files array
   * @param $type string
   * @param $cacheFile string
   * @return boolean
   */
  
  public function compareFiles ( $filesDir, $cacheDir, $files, $type, $cacheFile ) {

    if ( !file_exists($cacheDir . '/' . $cacheFile) ) {
      return false;
    }

    $cacheModified  = filemtime($cacheDir . '/' . $cacheFile);
    $extensionFile  = ($type == 'js') ? '.js' : '.css';

    foreach ($files as $script) {
      $sourceModified = filemtime($filesDir . '/' . $script . $extensionFile);
      if ( $sourceModified > $cacheModified ) {
        return false;
      }
    }

    return true;

  }

  /*
   * Minify CSS
   * 
   * @param $content string
   * @return string
   */
   public function minifyCSS ( $content ) {
     
      $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);

      /* Remove tabs, spaces, newlines, etc. */
      $content = str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), '', $content);

      /* Remove other spaces before/after ; */
      $content = preg_replace(array('(( )+{)','({( )+)'), '{', $content);
      $content = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $content);
      $content = preg_replace(array('(;( )+)','(( )+;)'), ';', $content);

      return $content;

   }

}
