function SnoOpy_Evernote( $post_id ) {
	$snoopy_palace="/home/snoopy/public_html"; // where the log file in debug mode will be created
	$eveDeb = false; // debug mode ?
	require_once 'snoopy-evernote/snoopy-evernote.php';
	//Update post
	global $wpdb;
	global $post;
	// select the post will be updated if it is there
	$eveQue = "SELECT ID,post_date,post_name FROM `wp_posts` WHERE post_title = '$postTit' AND post_status IN ('publish', 'pending', 'draft') AND post_type = 'post' AND ID != $post_id";
	//$everRes = $wpdb->get_results($wpdb->prepare("$eveQue"));
	$everRes = $wpdb->get_results("$eveQue");
	if ($eveDeb) {
		var_dump($everRes);
		die();
	}
	if(is_array($everRes))
	if(count($everRes) == 1) {
		// it there, so update this post "revision", instead of create new one
		$oID = $everRes[0]->ID;
		$custom = get_post_custom($oID);
		$feat_slider = $custom["feat_slider"][0];
		$creDate = $everRes[0]->post_date;
		$postName = $everRes[0]->post_name;
		if ($eveDeb) {
			echo "postNameOld: $postName";
			$fh1 = fopen("$snoopy_palace/snoopy-evernote.log", "a");
			fwrite($fh1, "postNameOld: $postName");
		}
		if ( $postName == "" ) {
			$postName = sanitize_title_with_dashes("$postTit");
		}
		if ($eveDeb) {
			echo "postNameNew: $postName";
			fwrite($fh1, "postNameNew: $postName");
			fclose($fh1);
			die();
		}
		// the update is actual, delete the existing post, and create new one, but retain the "created date" info
		wp_delete_post($oID, true);
		wp_update_post( array(
		'ID'    =>  $post_id,
		'post_date'   =>  $creDate,
		'post_name' => $postName
		));
		if ($eveDeb) $fh2 = fopen("$snoopy_palace/snoopy-evernote.log", "a");
		// support for Featured Content Slider
		if ($feat_slider == 1) {
			if ($eveDeb) fwrite($fh2, "feat_slider custom0 $feat_slider $post_id $oID");
			update_post_meta($post_id, 'feat_slider', 1);
		}
		// else {
		if ($eveDeb) fwrite($fh2, "NULL feat $post_id $postName");
		// }
		if ($eveDeb) fclose($fh2);
	}
	// create the hierarchical category for the post
	if ($snoopy_evernote) {
		//Check and create stack category
		if ( $stack != '' ) {
			$parCat = get_cat_ID($stack);
			if ( !$parCat > 0 ) {
				$parCat = wp_create_category( $stack );
			}
		}
		//Now is actual category
		$catID = array();
		$theCat = get_cat_ID($bookname);
		if ( !$theCat > 0 ) {
			if ( $stack != '' ) {
				$theCat = wp_create_category( $bookname, $parCat );
			}
			else {
				$theCat = wp_create_category( $bookname );
			}
		}
		array_push($catID, $theCat);
		wp_set_object_terms( $post_id,  $catID, 'category' );
		if ( $atag != '' ) {
			wp_set_post_tags( $post_id, $atag );
		}
		// wp_update_post( array(
		// 'ID'    =>  $post_id,
		// 'post_status'   =>  'draft'
		// ));
		//sourceURL
		$postCon = get_post_field( 'post_content', $post_id );
		if ($eveDeb) {
			fwrite($fh, "postCon: $postCon");
			`echo "postCon: $postCon" >> $snoopy_palace/snoopy-evernote.log`;
		}
		// sanitize the Evernote promo, extra HTML and generate the cute source URL info
		if ( $sourceURL != '' ) {
			$sourceURLhtml = '<td style=\"text-align: right;\"><h5><em>Source: <a href=\"'.$sourceURL.'\" target=\"_blank\">'.$sourceURL.'</a></em></h5></td>';
			$postCon = preg_replace( '|<td>Evernote helps.+\.</td>|', $sourceURLhtml, $postCon );
		}
		else {
			$postCon = preg_replace( '|\r?\n<table.+\r?\n<tr>\r?\n<td>.+\.</td>\r?\n</tr>\r?\n</table>|', '', $postCon );
			$postCon = preg_replace('|\r?\n<table.+\r?\n<tbody>\r?\n<tr>\r?\n<td>.+\.</td>\r?\n</tr>\r?\n</tbody>\r?\n</table>|', '', $postCon );
		}
		$postCon = preg_replace( '|<table.+\r?\n<tr>\r?\n<td></td>\r?\n</tr>\r?\n<tr>\r?\n<td><h1>.+</h1></td>\r?\n</tr>\r?\n</table>\r?\n|', '', $postCon );
		$postCon = preg_replace( '|<table.+\r?\n<tbody>\r?\n<tr>\r?\n<td></td>\r?\n</tr>\r?\n<tr>\r?\n<td>\r?\n<h1>.+</h1>\r?\n</td>\r?\n</tr>\r?\n</tbody>\r?\n</table>\r?\n|', '', $postCon );
		if ($eveDeb) {
			`echo "postConSan: $postCon" >> $snoopy_palace/snoopy-evernote.log`;
			fwrite($fh, "postConSan: $postCon");
			`echo "sourceURL: $sourceURLhtml" >> $snoopy_palace/snoopy-evernote.log`;
			fclose($fh);
		}
		// finalize and publish the "note post"
		if ( !isset($postName) || $postName == "" ) {
			$postName = sanitize_title_with_dashes("$postTit");
			wp_update_post( array(
			'ID'    =>  $post_id,
			'post_name' => $postName,
			'post_content'   =>  $postCon
			));
		}
		else {
			wp_update_post( array(
			'ID'    =>  $post_id,
			'post_content'   =>  $postCon
			));
		}
		wp_publish_post( $post_id );
	}
}
add_action('pending_post', 'SnoOpy_Evernote');