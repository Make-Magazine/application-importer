<?php
/**
 * Allows us to convert JSON/CSV/Spread Sheets and generate an XML file for the import of WordPress.
 *
 * Save this file into the root of your WordPress install to run. Other wise, modify the file path for the wp-load.php.
 *
 * EG. Use this URL to generate the XML. Or use cURL to save the file.
 * http://vip.dev/import-applications/index.php?file_name=template.csv&type=exhibit&url=http://vip.dev/import-applications/imports/
 * curl "http://vip.dev/import-applications/index.php?file_name=template.csv&type=exhibit&url=http://vip.dev/import-applications/imports/" -o "makerfaire-exibit-import.xml"
 *
 * @version 1.2
 */

// Load the WP bootstrap. TODO: update to auto grab relative URL instead of hard coding it... :/
require( '../wp/wp-load.php' );


/**
 * Set our variables.
 *
 * $file_name is required.
 * $url defaults to 'http://localhost/', if you wish to point this to another directory, you can do 'http://localhost/imports/'.
 *
 * @version  1.2
 */
$file_name = ( ! empty( $_REQUEST['file_name'] ) ? $_REQUEST['file_name'] : null );
$url 	   = ( ! empty( $_REQUEST['url'] ) ? $_REQUEST['url'] : null );
$type 	   = ( ! empty( $_REQUEST['type'] ) ? $_REQUEST['type'] : null );


// Check that we have passed the $file_name through a query string. If not, kill the script and send an error.
if ( empty( $file_name ) ) {
	echo "Error! File Name required! Use '?file_name=spread-sheet-name-here.csv' at the end of the URL.\n"; // Add the \n for proper console rendering.
	die();
}

// Check that we set a type.
if ( empty( $type ) ) {
	echo "Error! Type required! Use '&type=exhibit' or '&type=sponsor' at the end of the URL.\n"; // Add the \n for proper console rendering.
	die();
}


/**
 * Change the names of categories be dashed, rather then have spaces.
 * @param    String $str The name of the category
 * @return   String
 *
 * @version  1.0
 * @since    1.0
 */
function mf_convert_to_dashes( $str ) {

	// Convert the white space in the string to dashes
	$dashed = str_replace( ' ', '-', $str );

	return $dashed;
}


/**
 * The mother ship. This function will open and read a CSV file and convert it to a useable array.
 * By default the function will locate the file in the root of the local server.
 * @param  	 String $file_name The string containing the name of the CSV file.
 * @param  	 String $url 	   The URL where to find the CSV file.
 * @return 	 Array
 *
 * @version  1.0
 * @since    1.0
 */
function mf_get_the_file( $file_name, $url ) {
	global $type;

	// Check if the $url variable is empty. If so, set a default string.
	if ( empty( $url ) )
		$url = 'http://vip.dev/application-importer/imports/';

	// Create an empty array so we can add another array inside.
	$results = array();

	$results = array();

	if ( strpos( $file_name, '.js') ) {
		$json 		= file_get_contents( $url . $file_name );
		$results[] 	= json_decode( $json, true );
	}

	return $results;

	// // Use fopen() to read the file. NOTE, make sure spreadsheet is converted to a CSV file.
	// if ( ( $file = fopen( $url . $file_name, 'r' ) ) !== false ) {

	// 	// Loop through every row using a comma delimmited separator.
	// 	while ( ( $data = fgetcsv( $file, 0, ',' ) ) !== false ) {

	// 		// Setup our array containing all of the values in the CSV.
	// 		// NOTE: There were a lot of extra things not visable in the original spreadsheet?
	// 		if ( $type == 'exhibit' ) {
	// 			$columns = array(
	// 				'group'					=> $data[ $i ],  // GROUP WITH
	// 				'faire'					=> $data[1],  // EVENT
	// 				'form_type' 			=> $data[2],  // FORM TYPE
	// 				'status' 				=> $data[3],  // STATUS
	// 				'cats'					=> $data[5],  // CATEGORY
	// 				'sales'					=> $data[6],  // SALES
	// 				'location'				=> $data[7],  // PLACEMENT REQUEST (should be the location ID)
	// 				'project_name' 			=> $data[8],  // PROJECT NAME
	// 				'public_description'	=> $data[9],  // PUBLIC DESCRIPTION
	// 				'project_photo'			=> $data[10], // PROJECT PHOTO
	// 				'project_website'		=> $data[11], // PROJECT WEBSITE
	// 				'name'				    => $data[12], // MAKER NAME
	// 				'email'					=> '', // MAKER EMAIL
	// 				'photo'					=> '', // MAKER PHOTO
	// 				'city'					=> $data[15], // CITY
	// 				'state'					=> $data[16], // STATE
	// 				'zip'					=> $data[17], // ZIP
	// 				'country'				=> $data[18], // COUNTRY
	// 				'first_time'			=> $data[19], // FIRST TIME
	// 				'tags'					=> '', // TAGS
	// 			);
	// 		} elseif ( $type == 'sponsor' ) {
	// 			$columns = array(
	// 				'status' 				=> $data[3],
	// 				'form_type' 			=> $data[2],
	// 				'project_name' 			=> $data[8],
	// 				'public_description'	=> $data[9],
	// 				'project_photo'			=> '',
	// 				'project_website'		=> $data[11],
	// 				'group'					=> $data[0],
	// 				'cats'					=> '',
	// 				'tags'					=> '',
	// 				'name'				    => '',
	// 				'email'					=> '',
	// 			);

	// 		}

	// 		$results[] = $columns;
	// 	}

	// 	// Close the read connect
	// 	fclose( $file );
	// }

	// Send back the array of arrays :P
	// return $results;
}


/**
 * Generates the actual XML. Pretty stright forward, just dump the XML striaght into the document...
 * @param   Array $results
 * @return  XML
 *
 * @version 1.2
 * @since   1.0
 */
function mf_generate_xml( $results ) {

	global $type;

	$i = 0;
	// Loop through the results from mf_get_the_file and use array_slice() to remove the first array as we don't need it.
	foreach ( $results[0] as $column ) {
		// var_dump( $column );
		echo "\t<item>\n";
		echo "\t\t<title>" . wp_specialchars( $column['PROJECT_NAME'] ) . "</title>\n";
		echo "\t\t<pubdate>" . date( 'r' ) . "</pubdate>\n";
		echo "\t\t<dc:creator>makemagazine</dc:creator>\n";
		$name = 'MAKER NAME';
		echo "\t\t" . '<content:encoded><![CDATA[{"form_type":"' . strtolower( $column['TYPE'] ) . '","maker_faire":"2014_bayarea","uid":"","tags":"' . $column['TAGS'] . '","cats":"' . $column['CATEGORIES'] . '","project_name":"' . ent2ncr( esc_html( $column['PROJECT_NAME'] ) ) . '","private_description":"","public_description":"' . ent2ncr( esc_html( $column['PUBLIC_DESCRIPTION'] ) ) . '","project_photo":"' . $column['PROJECT_PHOTO'] . '","project_photo_thumb":"","project_website":"' . $column['PROJECT_WEBSITE'] . '","project_video":"","food":"","food_details":"","sales":"","sales_details":"","booth_size":"","booth_size_details":"","tables_chairs":"","tables_chairs_details":"","layout":"","activity":"","placement":"","booth_location":"","booth_options":"","lighting":"","noise":"","power":"","what_are_you_powering":"","amps":"","amps_details":"","internet":"","radio":"","radio_frequency":"","radio_details":"","fire":"","hands_on":"","safety_details":"","email":"' . $column['EMAIL'] . '","name":"' . $column[ $name ] . '","maker":"One maker","maker_name":"' . $column[$name] . '","maker_email":"' . $column['EMAIL'] . '","maker_photo":"' . $column['PROJECT_PHOTO'] . '","maker_photo_thumb":"","maker_bio":"","m_maker_name":[""],"m_maker_email":[""],"m_maker_photo":[""],"m_maker_photo_thumb":"","m_maker_bio":[""],"m_maker_gigyaid":[""],"group_name":"","group_bio":"","group_photo":"","group_photo_thumb":"","group_website":"","phone1":"","phone1_type":"","phone2":"","phone2_type":"","private_address":"","private_address2":"","private_city":"","private_state":"","private_zip":"","private_country":"","org_type":"","large_non_profit":"","supporting_documents":"","references":"","referrals":"","hear_about":"","first_time":"","anything_else":""}]]></content:encoded>' . "\n";
		echo "\t\t<wp:post_date>" . date( 'Y-m-d' ) . "</wp:post_date>\n";
		echo "\t\t<wp:comment_status>closed</wp:comment_status>\n";
		echo "\t\t<wp:ping_status>closed</wp:ping_status>\n";
		// echo "\t\t<wp:post_name>" . strtolower( preg_replace( array( '/[^a-z0-9\- ]/i', '/[ \-]+/' ), array( '', '-' ), $column['project_name'] ) ) . "</wp:post_name>\n"; // Comment this out as it can create duplicate URLs. Just let WP create the URL
		echo "\t\t<wp:status>accepted</wp:status>\n";
		echo "\t\t<wp:post_type>mf_form</wp:post_type>\n";
		echo "\t\t<wp:post_parent>0</wp:post_parent>\n";
		echo "\t\t<wp:menu_order>0</wp:menu_order>\n";
		echo "\t\t<wp:post_password></wp:post_password>\n";
		echo "\t\t<wp:is_sticky>0</wp:is_sticky>\n";
		if ( ! empty( $column['CATEGORIES'] ) ) {
			$cats = explode( ',', $column['CATEGORIES'] );
			foreach ( $cats as $cat ) {
				echo "\t\t<category domain=\"category\" nicename=\"" . sanitize_title( strtolower( $cat ) ) . "\"><![CDATA[" . $cat . "]]></category>\n";
			}
		}
		echo "\t\t<category domain=\"type\" nicename=\"" . strtolower( $column['TYPE'] ) . "\"><![CDATA[" . strtolower( $column['TYPE'] ) . "]]></category>\n";
		echo "\t\t<category domain=\"group\" nicename=\"" . strtolower( $column['GROUP'] ) . "\"><![CDATA[" . $column['GROUP'] . "]]></category>\n";
		echo "\t\t<category domain=\"faire\" nicename=\"maker-faire-bay-area-2014\"><![CDATA[Maker Faire Bay Area 2014]]></category>\n";
		echo "\t\t<wp:postmeta>\n";
		echo "\t\t\t<wp:meta_key>_ef_editorial_meta_checkbox_email-notifications</wp:meta_key>\n";
		echo "\t\t\t<wp:meta_value><![CDATA[1]]></wp:meta_value>\n"; // 1 checks the box which DISABLES auto responders
		echo "\t\t</wp:postmeta>\n";
		echo "\t\t<wp:postmeta>\n";
		echo "\t\t</wp:postmeta>\n";
		echo "\t\t<wp:postmeta>\n";
		echo "\t\t\t<wp:meta_key>_mf_form_type</wp:meta_key>\n";
		echo "\t\t\t<wp:meta_value><![CDATA[" . $column['TYPE'] . "]]></wp:meta_value>\n";
		echo "\t\t</wp:postmeta>\n";
		echo "\t\t<wp:postmeta>\n";
		echo "\t\t\t<wp:meta_key>_mf_log</wp:meta_key>\n";
		if ( $type == 'exhibit' ) {
			echo "\t\t\t<wp:meta_value><![CDATA[a:2:{i:0;s:26:\"" . date( 'm/d/y h:i a' ) ." Proposed\";i:1;s:46:\"" . date( 'm/d/y h:i a' ) ." Accepted by katemrowe\";}]]></wp:meta_value>\n";
		} elseif ( $type == 'sponsor' ) {
			echo "\t\t\t<wp:meta_value><![CDATA[a:2:{i:0;s:26:\"" . date( 'm/d/y h:i a' ) ." Proposed\";i:1;s:46:\"" . date( 'm/d/y h:i a' ) ." Accepted by mirandalinm\";}]]></wp:meta_value>\n";
		}
		echo "\t\t</wp:postmeta>\n";
		echo "\t</item>\n";
		$i++;
	}
}

// Spit out the header with the correct content type.
header("Content-type: text/xml; charset=utf-8");

// Echo this out first, or else XML errors will occur.
echo '<?xml version="1.0" encoding="UTF-8" ?>';

?>
<rss version="2.0" xmlns:excerpt="http://wordpress.org/export/1.2/excerpt/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:wp="http://wordpress.org/export/1.2/">
	<channel>
		<title>Maker Faire Exhibits</title>
		<link>http://vip.dev/makerfaire</link>
		<description></description>
		<pubDate><?php date( 'l jS \of F Y h:i:s A' ); ?></pubDate>
		<language>en-US</language>
		<wp:wxr_version>1.2</wp:wxr_version>
		<wp:base_site_url>http://vip.dev/makerfaire</wp:base_site_url>
		<wp:base_blog_url>http://vip.dev/makerfaire</wp:base_blog_url>
			<?php
				$file = mf_get_the_file( $file_name, $url );
			 	mf_generate_xml( $file ); // Spit out the XML! ?>
	</channel>
</rss>