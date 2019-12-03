## DW Q&A Comments

A few weeks ago I came across the [DW Q&A WordPress plugin](  https://www.nutt.net/out/dwqa-wordpress-plugin/ ) from Design Wall. With a quick activation it replaced bbPress with a discussion board similar to Stack Overflow.  A few tweaks to the templates and it looked just like I wanted.

But that wasn't enough. I wanted to replace comments on individual posts with their own mini discussion board. And that's where this plugin comes in.

### Installation

Head over to [releases]( https://github.com/RyanNutt/dwqa-wordpress-comments/releases ) and grab the latest zip file.

In WordPress admin click on the Plugins > Add New.

Click on the Upload Plugin and upload the zip you downloaded. 

### Shortcodes

This plugin adds a `[dwqa_post_comments]` shortcode that is replaced with any questions tied to that post and the standard comment submission form from DWQA. When on a post or page the shortcode adds a hidden field to the submission form so that it can be tied to that specific post. 



### Replacing Comments

Easiest way to replace all comments with this is to modify the `comments.php` file in your theme.

This is the one that I'm using.

```php
<?php
if ( post_password_required() )
  return;

echo '<div class="comments">' . do_shortcode( '[dwqa_post_comments]' ) . '</div>';
```



### Why?

I've never really liked the default WordPress comments or any of the other JavaScript options. The Stack Overflow interface has always seemed more intuitive to me, so I wanted to go with that. 