<?php
	/*
Plugin Name: Antracks
Plugin URI:
Description: The new YouTube playlist plugin for Wordpress
Version: .1
Author: AnyTv
Author URI: 
License:
*/
/*  Copyright 2013  OpenKit 

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



//connect database, change username and password to your corresponding database username and password
    mysql_connect("localhost", "root", "") or die('Go to your wordpress folder->wp-content->plugins->newantracks. Open newantracks.php on notepad. On line 26 of newantracks.php, edit the localhost, username, and password to your corresponding database localhost name, username, and password. For step by step instructions, please visit http://wordpress.org/plugins/antracks/installation/'.mysql_error());   
    mysql_select_db("wordpress") or die(mysql_error());	  

	//create table
    if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '%antable'"))==1) 
    {
			//table euerrxist
    }
    else {
    	$sql="CREATE TABLE antable(plname CHAR(50), pllink CHAR(255), autoplay INT(1))";
    	if (mysql_query($sql))
    	{
    		echo "Table antable created successfully";
    	}
    	else
    	{
    		echo "Error creating table: " . mysql_error();
    	}
    }
//database area ends here

//add to settings
    add_action('admin_menu', 'antracks_admin_actions');

    function antracks_admin_actions(){
    	add_options_page('antracks','antracks','manage_options', _FILE_, 'antracks_admin');

    }

//GLOBAL VARIABLE
    $result5=array();

//this will be displayed on the Antracks Admin Settings, the admin will record the playlists' name, url and enable or disable autoplay
    function antracks_admin(){
    	?>
    	<div class="wrap">
    		<h4>
    			ANTRACKS: a new YouTube Playlist GUI plugin for WordPress
    		</h4>
    		<form action="" method="POST">
    			Name Playlist: <input type="text" name="playname" size="25"> <br>
    			YouTube Playlist URI:
    			<input type="text" name="playlistlink" size="40"> 
    			<br>

    			<small>copy and paste this shortcode to your post or page= [antracks]</small><br>
    			<?php
    			//store playlist name and link on a variable
    			$pname=$_POST['playname'];
    			$try=$_POST['playlistlink'];
    			$explaylist=explode("&list=", $try);
    			echo $explaylist[0]."<br>".$explaylist[1];
    			//check if the values submitted are empty or not
    			if($try!=NULL && $try!=NULL){
    				$arr2=$explaylist[1];
    				$playid = $explaylist[1];
    				//$arr3 = strtr ($arr2, array ('&' => '?'));

				//record to database
    				$check= mysql_query("SELECT plname FROM antable WHERE plname='$pname'");
    				if(mysql_num_rows($check) != 0)
    				{
					//do nothing if playlist name already exist
    				}
    				else
    				{
    					mysql_query("INSERT INTO antable (plname, pllink, autoplay)VALUES ('$pname','$playid','0')");
    				}

    				echo "</div>";

    			}
		//choose playlist for shortcode from the saved playlists:
    			$code = mysql_query("SELECT plname,autoplay FROM antable");
    			echo "<br>Choose a playlist: ";
    			echo "<select name='playchoose'><option value=' '></option>";
    			while($rowx = mysql_fetch_array($code))
    			{
    				echo "<option value='".$rowx['plname']."'>";
    				echo $rowx['plname'];	
    				$aplayvalue = $rowx['autoplay'];			 	  		 	  
    				echo "<br>";
    			}
    			echo "<br></select>Activate Autoplay: <input type='radio' name='autoplayc' value='1'>Yes<input type='radio' name='autoplayc' value='0'>No<br><input type='submit' name='submit' value='Submit'></form>";
    			if($_POST['playchoose']!=' '){
    				echo "<br><br>Copy paste this to your page or post: <br><textarea>[antracks id='".$_POST['playchoose']."']</textarea>";
    				echo "<br>Autoplay: ";


    				$chosenplist = $_POST['playchoose']; ;
    				//update autoplay status to daytabase
    				$update = mysql_query("UPDATE antable SET autoplay='".$_POST[autoplayc]."' where plname='".$chosenplist."'");
    				$query = mysql_query("SELECT autoplay FROM antable where plname='".$chosenplist."'");
    				$datax = mysql_fetch_array($query);								
    				if($datax['autoplay']==1){
    					echo "YES";
    				}
    				else{
    					echo "NO";
    				}									
    			}


} //antracks_admin() ends here.

function foobar_func($atts){
	wp_enqueue_style( 'newantracks', plugins_url( 'tglobal.css', __FILE__ ), false, false, 'all' );
	extract( shortcode_atts( array(
		'id' => '$atts',		
		), $atts ) );
	$resultfoo = mysql_query("SELECT pllink,autoplay FROM antable WHERE plname='$id'");	
	$row = mysql_fetch_array($resultfoo);
			//echo $row['pllink'];
	function getYoutubeImage($e){
						//GET THE URL
		$url = $e;
		$queryString = parse_url($url, PHP_URL_QUERY);
		parse_str($queryString, $params);
		$v = $params['v'];  
						//DISPLAY THE IMAGE
		if(strlen($v)>0){
			echo "<img src='http://i3.ytimg.com/vi/$v/default.jpg' width='100' />";
		}
	}

	$playid = $row['pllink'];
				//$var = $row['autoplay']


	$xml = simplexml_load_file("http://gdata.youtube.com/feeds/api/playlists/$playid");
	echo "</div>";
	?>
	<script type='text/javascript'>
	var playvid;
	var currentvid;
	function click(ren){
		var nee = ren.toString();
		currentvid = ren;
		var val = document.getElementById("clickthis"+nee).getAttribute("value");
		var val3 = document.getElementById("vidauthor"+nee).getAttribute("value");
		playvid = val;
		var auto = "?autoplay=1";
		var prefx="<iframe id='video'style='background-color:#1b1b1b;' width='640' height='390' src='http://www.youtube.com/embed/";
		var sufx="'' frameborder='0' allowfullscreen showinfo=0&autohide=0></iframe>";
		document.getElementById('videobox').innerHTML=prefx+val+auto+sufx;
		document.getElementById('vidcount').innerHTML=currentvid+1;
	}

	function floaded(){
		player = new YT.player('player',{
			videoid : playvid,
			events:{
				'onStateChange':function(event){
					alert(event.data);
				}
			}
		});
	}
	</script>

	<div id="tank">
		<div id="titlebar">
			<div id="vidtitle">
				<?php 
				$plid=(string)$xml->children('yt', true)->playlistId;
				$authorname=(string)$xml->author->name;
				echo "<span id='pltitle'><a id='pllink' href='http://www.youtube.com/playlist?list=".$plid."'>".(string)$xml->title."</a></span>";
				echo " <span id='plauthor'>by </span><a href='http://www.youtube.com/".$authorname."'><span id='plauthorlink'>".$authorname.'</a></span>';						
				?>
			</div>
			<div id="countdisplay">
				<?php
				$r=0;
				$result3=array();
				$result4=array();
				$title=array();
				foreach($xml->entry as $value){
					$result3[$r] =  $value->link->attributes()->href;
					$title[$r] =  $value->title;
					$result4[$r] = substr($result3[$r],31,-22);
					$result5[$r] = substr($result3[$r],31,-22);
					$number = $r;
					if($result4[$r]!=NULL){
						$r++;
					}
				}							
				$queryy = mysql_query("SELECT autoplay FROM antable where plname='".$id."'");
				$dataxx = mysql_fetch_array($queryy);								
				$aplay = $dataxx['autoplay'];				
				?>
				<span id="vidcount">1</span><span style="font-family:Arial;"><?php echo ' / '.$r; ?></span>
			</div>
		</div>
		<div id="videobar">
			<div id="videobox">
				<iframe id="video" style="background-color:#1b1b1b;" width="640" height="390" onload="floaded()" src="<?php echo "http://www.youtube.com/embed/$result4[0]?autoplay=$aplay";?>" frameborder="0" allowfullscreen showinfo='0' rel="0" >
				</iframe>
			</div>
			<div id="videotray">
				<?php
				$r=0;
				$result3=array();
				$result4=array();
				$title=array();
				foreach($xml->entry as $value){
					$result3[$r] =  $value->link->attributes()->href;
					$result6[$r] =  $value->author->name;
					$title[$r] =  $value->title;
					$result4[$r] = substr($result3[$r],31,-22);
					$result5[$r] = substr($result3[$r],31,-22);
					$number = $r+1;
					if($result4[$r]!=NULL){
						echo "<div id='num'>".$number."</div>";
						echo "<a href='javascript:click(".$r.")' name='clickthis' id='clickthis".$r."' value='".$result4[$r]."'><span id='ytimage'>";
						getYoutubeImage("http://www.youtube.com/watch?v=$result4[$r]");
						echo "</span><div id='trayvid' >".$title[$r]."</a><br><span id='ctitle".$r."' value='".$title[$r]."'><span id='vidauthor".$r."' value='".$result6[$r]."'>by ".$result6[$r]."</span></span></div>";
						echo "<br>";
						$r++;
					}
					
				}							
				$queryy = mysql_query("SELECT autoplay FROM antable where plname='".$id."'");
				$dataxx = mysql_fetch_array($queryy);								
				$aplay = $dataxx['autoplay'];				
				?>
			</div>
				<!-- <div id="footerbar">

			</div> -->
		</div>
	</div>
	<?php
}

add_shortcode( 'antracks', 'foobar_func' );
?>