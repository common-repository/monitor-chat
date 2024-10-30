<?php
if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

function monitorchat_shortcodes_table($title,$shortcodes){
$shortcode_array = explode(',', $shortcodes);
if(in_array('none',$shortcode_array)){return;} // just leave if 'none' is in array
?>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
  overflow:hidden;padding:10px 5px;word-break:normal;}
.tg th{border-color:black;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;
  font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
.tg .tg-nqae{background-color:#ecf4ff;border-color:#343434;color:#343434;font-weight:bold;text-align:left;vertical-align:top}
.tg .tg-sx4j{background-color:#ffffff;border-color:#343434;color:#343434;text-align:left;vertical-align:top}
</style>
<h3>Shortcodes supported in the <?php echo $title; ?> message.</h3>
<table class="tg">
<thead>
  <tr>
    <th class="tg-nqae">Shortcode</th>
    <th class="tg-nqae">Returns</th>
    <th class="tg-nqae">Info</th>
  </tr>
</thead>
<tbody>
<?php 
if(in_array('EMOJI',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;SMILEY&gt;</td>
    <td class="tg-sx4j">Emoji</td>
    <td class="tg-sx4j">Over 500 <a href="https://monitor.chat/documentation/command_line/emojis/" target="_blank" rel="noopener noreferrer">emoji shortcodes</a> are available in Monitor.chat!</td>
  </tr>
<?php }
if(in_array('USEREMAIL',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USEREMAIL&gt;</td>
    <td class="tg-sx4j">Wordpress Email of User</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('USERNAME',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERNAME&gt;</td>
    <td class="tg-sx4j">Wordpress User Name</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('USERFIRSTNAME',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERFIRSTNAME&gt;</td>
    <td class="tg-sx4j">Wordpress First Name</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('USERLASTNAME',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERLASTNAME&gt;</td>
    <td class="tg-sx4j">Wordpress Last Name</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('USERDISPLAYNAME',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERDISPLAYNAME&gt;</td>
    <td class="tg-sx4j">Wordpress Display Name</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('USERID',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERID&gt;</td>
    <td class="tg-sx4j">Wordpress User ID</td>
    <td class="tg-sx4j">An integer value.</td>
  </tr>
<?php }
if(in_array('USERROLES',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERROLES&gt;</td>
    <td class="tg-sx4j">Wordpress Role(s) of User</td>
    <td class="tg-sx4j"></td>
  </tr>
<?php }
if(in_array('USERLEVEL',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <!-- tr>
    <td class="tg-sx4j">&lt;USERLEVEL&gt;</td>
    <td class="tg-sx4j">Wordpress User Level</td>
    <td class="tg-sx4j">Levels were replaced by roles in Wordpress 3.0.</td>
  </tr -->
<?php }
if(in_array('USERIPADDRESS',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERIPADDRESS&gt;</td>
    <td class="tg-sx4j">IP Address of User</td>
    <td class="tg-sx4j">IP address from which user is accessing website.</td>
  </tr>
<?php }
if(in_array('USERAGENT',$shortcode_array)||(in_array('userattribs',$shortcode_array))){ ?>
  <tr>
    <td class="tg-sx4j">&lt;USERAGENT&gt;</td>
    <td class="tg-sx4j">Agent of the User</td>
    <td class="tg-sx4j">Information about the browser and OS of the user.</td>
  </tr>
<?php } 
if(in_array('post',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;AUTHOR&gt;</td>
    <td class="tg-sx4j">Author of post</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;TITLE&gt;</td>
    <td class="tg-sx4j">Title of post</td>
    <td class="tg-sx4j"></td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;CONTENT&gt;</td>
    <td class="tg-sx4j">Content	of post</td>
    <td class="tg-sx4j"></td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;EXCERPT&gt;</td>
    <td class="tg-sx4j">Excerpt of post</td>
    <td class="tg-sx4j"></td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;STATUS&gt;</td>
    <td class="tg-sx4j">Status of post</td>
    <td class="tg-sx4j"></td>
  </tr>
<?php }
if(in_array('pubpage',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;AUTHOR&gt;</td>
    <td class="tg-sx4j">Author of page</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;TITLE&gt;</td>
    <td class="tg-sx4j">Title of page</td>
    <td class="tg-sx4j"></td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;CONTENT&gt;</td>
    <td class="tg-sx4j">Content of page</td>
    <td class="tg-sx4j"></td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;STATUS&gt;</td>
    <td class="tg-sx4j">Status of page</td>
    <td class="tg-sx4j"></td>
  </tr>
<?php }
if(in_array('comment',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;AUTHOR&gt;</td>
    <td class="tg-sx4j">Author of Comment</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('comment',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;AUTHOREMAIL&gt;</td>
    <td class="tg-sx4j">Email address of author.</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('comment',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;COMMENT&gt;</td>
    <td class="tg-sx4j">Comment</td>
    <td class="tg-sx4j"></td>
  </tr>
<?php }
if(in_array('comment',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;POSTTITLE&gt;</td>
    <td class="tg-sx4j">Title of Post in which the comment was added.</td>
    <td class="tg-sx4j"></td>
  </tr>
<?php }
if(in_array('core',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;INSTALLEDVERSION&gt;</td>
    <td class="tg-sx4j">The installed version of Wordpress Core.</td>
    <td class="tg-sx4j"></td>
  </tr>
<?php } 
if(in_array('core',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;PREFERREDVERSION&gt;</td>
    <td class="tg-sx4j">The latest available version of Wordpress Core.</td>
    <td class="tg-sx4j"></td>
  </tr>
<?php } 
if(in_array('plugins',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;PLUGINSTOUPDATE&gt;</td>
    <td class="tg-sx4j">A text report of plugins that can be updated.</td>
    <td class="tg-sx4j"></td>
  </tr><?php } 
if(in_array('themes',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;THEMESTOUPDATE&gt;</td>
    <td class="tg-sx4j">A text report of themes that can be updated.</td>
    <td class="tg-sx4j"></td>
  </tr><?php }
if(in_array('term',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;TERMID&gt;</td>
    <td class="tg-sx4j">Term ID</td>
    <td class="tg-sx4j">Term ID is an integer.</td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;TERMNAME&gt;</td>
    <td class="tg-sx4j">Term Name</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;TERMSLUG&gt;</td>
    <td class="tg-sx4j">Term Slug</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;TERMDESC&gt;</td>
    <td class="tg-sx4j">Term Description</td>
    <td class="tg-sx4j"></td>
  </tr>
  <?php }
if(in_array('attachment',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;URL&gt;</td>
    <td class="tg-sx4j">URL to attachment file.</td>
    <td class="tg-sx4j"></td>
  </tr><?php }
if(in_array('updatereport',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;UPDATED&gt;</td>
    <td class="tg-sx4j">A brief report of what software elements have been updated.</td>
    <td class="tg-sx4j"></td>
  </tr><?php }
if(in_array('filesystemreport',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;FILESYSTEMREPORT&gt;</td>
    <td class="tg-sx4j">A report of file system space utilization of the Wordpress server.</td>
    <td class="tg-sx4j"></td>
  </tr><?php }
if(in_array('memoryreport',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;MEMORYREPORT&gt;</td>
    <td class="tg-sx4j">A report of current memory usage of the Wordpress server.</td>
    <td class="tg-sx4j"></td>
  </tr><?php }
if(in_array('updraft',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;UPDRAFTFILES&gt;</td>
    <td class="tg-sx4j">A list of Updraft backup files.</td>
    <td class="tg-sx4j"></td>
  </tr><?php }
if(in_array('gwolle',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;GWOLLE&gt;</td>
    <td class="tg-sx4j">The Gwolle guestbook entry.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;AUTHOREMAIL&gt;</td>
    <td class="tg-sx4j">The author's email address.</td>
    <td class="tg-sx4j">Masking of this PII value is available.</td>
  </tr>
<?php }
if(in_array('product',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;PRODUCTID&gt;</td>
    <td class="tg-sx4j">Product ID.</td>
    <td class="tg-sx4j">An integer.</td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;PRODUCTNAME&gt;</td>
    <td class="tg-sx4j">Product name.</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;PRODUCTSTATUS&gt;</td>
    <td class="tg-sx4j">Status.</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;PRODUCTDESC&gt;</td>
    <td class="tg-sx4j">Product description.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <?php }
if(in_array('coupon',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;COUPONCODE&gt;</td>
    <td class="tg-sx4j">The coupon code that was redeemed.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <?php }

if(in_array('summary',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;SUMMARY&gt;</td>
    <td class="tg-sx4j">A summary of recent orders.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <?php }
if(in_array('visitors',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;ONLINEVISITORS&gt;</td>
    <td class="tg-sx4j">A report of visitors currently online.</td>
    <td class="tg-sx4j">A visitor does not need to be logged into the website to be counted.</td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;VISITORSREPORT&gt;</td>
    <td class="tg-sx4j">A historical report of unique visitors.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <tr>
    <td class="tg-sx4j">&lt;VISITSREPORT&gt;</td>
    <td class="tg-sx4j">A historical report of visits.</td>
    <td class="tg-sx4j">A visitor may visit many times.</td>
  </tr>
  <?php }
if(in_array('referrals',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;REFERRALSREPORT&gt;</td>
    <td class="tg-sx4j">A report of referrals from the most popular search engines.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <?php }
if(in_array('toppages',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;TOPPAGESREPORT&gt;</td>
    <td class="tg-sx4j">A report of top performing pages of this Wordpress website.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <?php }
if(in_array('order',$shortcode_array)){ ?>
  <tr>
    <td class="tg-sx4j">&lt;ORDERID&gt;</td>
    <td class="tg-sx4j">An integer value.</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;STATUS&gt;</td>
    <td class="tg-sx4j">Current status of the order.</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;DESTINATION&gt;</td>
    <td class="tg-sx4j">Where the order is to be shipped.</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;CURRENCY&gt;</td>
    <td class="tg-sx4j">Currency of the order.</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;TOTAL&gt;</td>
    <td class="tg-sx4j">Total value of the order.</td>
    <td class="tg-sx4j"></td>
  </tr>
    <tr>
    <td class="tg-sx4j">&lt;PRODUCTLIST&gt;</td>
    <td class="tg-sx4j">A list of the items in the order.</td>
    <td class="tg-sx4j"></td>
  </tr>
  <?php }

 ?>

</tbody>
</table>
<?php
}
?>
