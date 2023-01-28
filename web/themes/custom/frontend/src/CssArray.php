<?php

/**
 * CssArray is a PHP function to convert CSS to Array.
 *
 * Example usage:
 * $css = file_get_contents($file);
 * $ca = new CssArray();
 * $cssArray = $ca->convert($css);
 * print_r($cssArray);
 *
 * @package CssArray
 * @version $Revision: 1.0 $
 * @access public
 */

class CssArray {
  /**
   * Start to process the css string.
   *
   * @param [string] $css
   *  [CSS style].
   *
   * @return [array]      [PHP Array containing CSS information].
   */
  public function convert($css) {
    $css = $this->cleanCSS($css);
    $css = $this->parseCSS($css);
    return $css;
  }

  /**
   * This is used to recursive the CSS style.
   *
   * @param [array] $style
   *   [CSS Style].
   * @param [array] $result
   *   [Result reference from the main function].
   */
  private function setStyleRecursive($style, &$result) {
    foreach ($style as $name => $val) {
      if (is_array($val)) {
        if (!isset($result[$name])) {
          $result[$name] = [];
        }
        $r = &$result[$name];
        $this->setStyleRecursive($val, $r);
      }
      else {
        $result[$name] = $val;
      }
    }
  }

  /**
   * Break @media CSS.
   *
   * @param [array] $style
   *   [CSS media style].
   *
   * @return [array]        [Array of the media CSS Style].
   */
  private function processMediaStyle($style) {
    $result = [];
    preg_match_all('/(?ims)([a-z0-9\s\,\.\:#_\-@*()\[\]"=]+)\{([^\}]*)\}(?:\s\})?/', $style, $matches);
    foreach ($matches[0] as $content) {
      $this->processContent($content, $result);
    }
    return $result;
  }

  /**
   * Process CSS @media Content.
   *
   * @param [string] $content
   *   [string that start with @media].
   * @param [array] $result
   *   [Result reference from the main function].
   *
   * @return [null]          [update the result directly].
   */
  private function processMediaContent($content, &$result) {
    $tmp = explode('{', $content);
    $mediaClass = explode(',', array_shift($tmp));
    $mediaStyleContent = $this->processMediaStyle(substr(implode('{', $tmp), 0, -1));
    foreach ($mediaClass as $mc) {
      $mc = trim(str_replace('@media', '', $mc));
      $tcl = explode(',', trim($mc));
      $r = &$result['@media'];
      foreach ($tcl as $tt) {
        $name = trim($tt);
        if (!isset($r[$name])) {
          $r[$name] = [];
        }
        $r = &$r[$name];
      }
      $this->setStyleRecursive($mediaStyleContent, $r);
    }
  }

  /**
   * Break Style into array.
   *
   * @param [string] $style
   *   [CSS Style].
   *
   * @return [array]        [Array contain the CSS style name and value].
   */
  private function processStyles($style) {
    $result = [];
    $tmp = explode(';', $style);
    foreach ($tmp as $s) {
      $st = explode(':', $s);
      $name = trim(array_shift($st));
      $val = trim(implode(':', $st));
      if ($val != '') {
        if (substr($val, -1) == '}') {
          $result[$name] = substr($val, 0, -1);
        }
        else {
          $result[$name] = $val;
        }
      }
    }
    return $result;
  }

  /**
   * Process CSS style (not for @media query CSS style)
   *
   * @param [string] $content
   *   [String content].
   * @param [array] $result
   *   [Reference from the main function].
   *
   * @return [null]          [Update the results directly using reference].
   */
  private function processContent($content, &$result) {
    if (strpos($content, '@media')) {
      $this->processMediaContent($content, $result);
    }
    else {
      $tmp = explode('{', $content);
      $class = trim($tmp[0]);
      $style = trim($tmp[1]);
      $styleArr = $this->processStyles($style);
      $tc = explode(',', $class);
      foreach ($tc as $cl) {
        $tcl = explode(' ', trim($cl));
        $r = &$result;
        foreach ($tcl as $tt) {
          if (!isset($r[trim($tt)])) {
            $r[trim($tt)] = [];
          }
          $r = &$r[trim($tt)];
        }
        $r = $styleArr;
      }
    }
  }

  /**
   * Parsing CSS (not legal way)
   * Using regular expression to break the css
   *
   * @param [string] $css
   *   [CSS content].
   *
   * @return [array]      [PHP array contain CSS information].
   */
  private function parseCSS($css) {
    $result = [];
    preg_match_all('/(?ims)([a-z0-9\s\,\.\:#_\-@*()\[\]"=]+)\{([^\}]*)\}(?:\s\})?/', $css, $matches);
    $countBraces = 0;
    $multiContent = '';
    foreach ($matches[0] as $content) {
      $countBraces += substr_count($content, '{');
      $countBraces -= substr_count($content, '}');
      if ($countBraces == 0) {
        if (!empty($multiContent)) {
          $multiContent .= $content;
          $this->processContent($multiContent, $result);
          $multiContent = '';
        }
        else {
          $this->processContent($content, $result);
        }
      }
      else {
        $multiContent .= $content;
      }
    }
    ksort($result);
    return $result;
  }

  /**
   *
   */
  private function removeSpace($matches) {
    return str_replace(' ', '', $matches[0]);
  }

  /**
   * Clean CSS. Normalize the string for this class to process.
   *
   * @param [string] $css
   *   [CSS string].
   *
   * @return [string] [Fixed CSS string].
   */
  private function cleanCSS($css) {
    // Remove font-face.
    $css = preg_replace('/\@font\-face\s?\{[^}]*\}/m', '', $css);

    // Remove import.
    $css = preg_replace('/\@import\surl\([^}]*\)\;/m', '', $css);

    // Escape base64 images.
    $css = preg_replace('/(data\:[^;]+);/i', '$1#&', $css);

    // Remove comments.
    $css = preg_replace('!/\*.*?\*/!s', '', $css);
    $css = preg_replace('/\n\s*\n/', "\n", $css);

    // Replace '>' to ' ' space.
    $css = preg_replace('/\>/m', ' ', $css);

    $find = [
      '::before',
      ':before',
      '::after',
      ':after',
      ':active',
      ':link',
      ':visited',
      ':first-child',
      ':last-child',
      ':nth-child',
      ':hover',
      ':focus',
      '._',
      '.',
      '#',
      '@media screen and',
      ') and (',
      '[',
    ];
    $replace = [
      ' :before',
      ' :before',
      ' :after',
      ' :after',
      ' :active',
      ' :link',
      ' :visited',
      ' :first-child',
      ' :last-child',
      ' :nth-child',
      ' :hover',
      ' :focus',
      ' ._',
      ' .',
      ' #',
      '@media',
      '), @media (',
      ' [',
    ];
    // Update the string.
    $css = str_replace($find, $replace, $css);

    // Fix number.
    $css = trim(
      preg_replace_callback(
        '/\d\s\.\d/m',
        [$this, 'removeSpace'],
        $css
      )
    );

    // Trim the strings.
    $css = trim(preg_replace('/\s+/', ' ', $css));

    return $css;
  }
}
