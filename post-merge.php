<?php
/*
Plugin Name: Post Merge
Plugin URI: http://github.com/ibotty/post-merge
Description: A plugin to merge two post.
Version: 0.1
Author: Tobias Florek <me@ibotty.net>
Author URI: http://github.com/ibotty
License: BSD
*/
?>

<?php
/*
Copyright (c) 2011, Tobias Florek.  All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice,
     this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice,
     this list of conditions and the following disclaimer in the documentation
     and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER ``AS IS'' AND ANY EXPRESS OR
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

require_once(ABSPATH . '/wp-admin/includes/plugin.php');
require_once(ABSPATH . '/wp-admin/includes/template.php');

if (! class_exists("PostMerge")) {
class PostMerge {

  function __construct() {
    $this->init();
  }

  function init() {
    register_activation_hook (__FILE__, array ($this, 'install'));
    register_deactivation_hook (__FILE__, array ($this, 'deinstall'));

    add_action("admin_enqueue_scripts", array($this, "admin_scripts"));

    add_filter("post_row_actions", array($this, "row_actions"), 10, 2);
    add_filter("page_row_actions", array($this, "row_actions"), 10, 2);

    add_filter("admin_head", array($this, "head"), 10, 2);
  }

  function admin_scripts() {
    wp_enqueue_script('pm-load_merge', plugins_url('pm-load_merge.js', __FILE__));
  }
  function install() {
  }
  function deinstall() {
  }

  function head() {
    if (isset($_GET['pm-candidate'])) {
      $candidate = intval($_GET['pm-candidate']);

      echo "<style type='text/css'> #post-$candidate {background:rgba(255,0,0,0.2);} </style>";
    }
  }

  function row_actions($actions, $post) {
    $cur_url = $_SERVER['REQUEST_URI'];
    $merge_url = 'tmp';

    # if a merge candidate is already set
    if (isset($_GET['pm-candidate'])) {
      $candidate = intval($_GET['pm-candidate']);

      # remove candidate status if same post
      if ($post->ID === $candidate) {
        $link = remove_query_arg('pm-candidate', $cur_url);
        $displaytext = 'Cancel Merge';
      } else { # merge
        $link = esc_url(add_query_arg(array(
          'pm-one'=>$candidate, 'pm-another'=>$post->ID), $merge_url));
        $displaytext = 'Merge with selected '.$_GET['post_type'];
      }
    } else { # no merge candidate set
      $link = esc_url(add_query_arg('pm-candidate', $post->ID, $cur_url));
      $displaytext = 'Merge';
    }
    $str = '<a class="pm-merge" href="'.$link.'">'.__($displaytext).'</a>';
    $actions["posts_merge"] = $str;
    return $actions;
  }

  function tools_scripts() {
    wp_enqueue_script('pm_tools.js', false, array('jquery', 'jquery-ui', false,
      true));
  }
  function tools_styles() {
    wp_enqueue_style('pm_tools.css');
  }
}
}
new PostMerge()

?>