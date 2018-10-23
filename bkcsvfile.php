<?php
ob_start();
include('wp-config.php');
include('wp-admin/includes/taxonomy.php');

/*$databasehost = "localhost"; 
$databasename = "csvtest"; 
$databaseusername ="root"; 
$databasepassword = ""; 
*/
$databasetable1 = "wp_posts"; 
$databasetable2 = "wp_postmeta"; 
$fieldseparator = "^"; 
$lineseparator = "\n"; 
$csvfile = "csvfile/products.csv"; 
$addauto = 0; 
$save = 0; 
$outputfile = "csvlogs.sql"; 

if(!file_exists($csvfile)) { 
    echo "File not found. Make sure you specified the correct path.\n"; 
    exit; 
} 

$file = fopen($csvfile,"r"); 
if(!$file) { 
    echo "Error opening data file.\n"; 
    exit; 
} 

$size = filesize($csvfile); 
if(!$size) { 
    echo "File is empty.\n"; 
    exit; 
} 

$csvcontent = fread($file,$size); 

fclose($file); 


$lines = 0; 
$queries = ""; 
$linearray = array(); 
$i = 0;
$csvcontent = str_replace("<br/>\n", " ", $csvcontent);
//$t = get_post_meta(12413,'_wp_attachment_metadata');
//$t = get_post_meta(12410,'_wpsc_product_metadata');
//echo "<pre>";print_r($t);die;

//$texonomy = wp_create_category('New',0);
//mysql_query('update wp_term_taxonomy set taxonomy = "wpsc_product_category" where term_taxonomy_id = '.$texonomy);
//die;

foreach(split($lineseparator,$csvcontent) as $line) {  
    if (empty($line)) { 
        break; 
    } 
	if($i == 0){
	$i++;
	continue;
	}
    $lines++; 
    $line = trim($line," \t"); 
    $line = str_replace("\r","",$line); 
    $line = str_replace("'","\'",$line); 
    $linearray = explode($fieldseparator,$line); 
	
	//echo "<pre>";print_r($linearray);
	$temp1 = array_slice($linearray, 1, 3);
	$temp2 = array_slice($linearray, 9, 9);
	$post_data = array_merge($temp1, $temp2);
	$title = utf8_encode($post_data[0]);
	
	$con = htmlspecialchars(utf8_encode($post_data[1]))."<br/>";
	$img = utf8_encode($post_data[2]);
	//print_r($post_data);
	$sku = array_slice($linearray, 0, 1);
	$sku = trim($sku[0], '"');
	$price = array_slice($linearray, 4, 1);
	$price = trim($price[0], '"');
	$sale_price = array_slice($linearray, 5, 1);
	$sale_price = trim($sale_price[0], '"');
	$qty = array_slice($linearray, 6, 1);
	$qty = trim($qty[0], '"');
	$weight = array_slice($linearray, 7, 1);
	$weight = trim($weight[0], '"');
	$tax_amt = array_slice($linearray, 8, 1);
	$tax_amt = trim($tax_amt[0], '"');
	$cat = trim($post_data[3],'"');
	//$catid = wp_create_category( $cat, 0 );
	
	// Create post object
  $my_post = array(
     'post_title' => trim($title,'"'),
     'post_content' => trim($con,'"'),
     'post_status' => 'publish',
     'post_author' => 1,
	 'post_type' => 'wpsc-product'
	// 'post_category' => array($catid)
  );

$wp_upload_dir = wp_upload_dir();
$upload_dir = $wp_upload_dir['path'];
  
$imgpath = $upload_dir."/".trim($img,'"');
$imgtitle = explode(".",trim($img,'"'));
//echo $wp_upload_dir['url'];
$files = explode("uploads/",trim($wp_upload_dir['url'],'"'));
$file = $files[1]."/".trim($img,'"');

$size = getimagesize($imgpath);
$guid = $wp_upload_dir['url'].'/'.$img;
//echo "<pre>";print_r($size);
$type = $size['mime'];
$width = $size[0];
$height = $size[1];
$hwstring_small = $size[3];


					$attachment = array (
                       "post_content"   => "",
                       "post_title"     => $imgtitle[0],
                       "post_name"      => sanitize_title($imgtitle[0]),
                       "post_status"    => 'inherit',
                       "post_type"      => 'attachment',          
                       "post_mime_type" => $type,
					   'guid' => $guid, 
                      );
$pro_metadata = '';
$pro_metadata = array(
						'wpec_taxes_taxable_amount' => $tax_amt,
						'external_link' => '',
						'external_link_text' => '',
						'external_link_target' => '',
						'weight' => $weight,
						'weight_unit' => 'pound',
						'dimensions' => 
									array(
										'height' => 0,
										'height_unit' => 'in',
										'width' => 0,
										'width_unit' => 'in',
										'length' => 0,
										'length_unit' => 'in',

									),
						'shipping' => array(
										'local' => 0,
										'international' => 0,
									),
						'merchant_notes' => '',
						'engraved' => 0,
						'can_have_uploaded_image' => 0,
						'enable_comments' => '',
						'unpublish_when_none_left' => 0,
						'no_shipping' => 0,
						'quantity_limited' => 0,
						'special' => 0,
						'display_weight_as' => 'pound',
						'table_rate_price' => array(
										'quantity' => array(
														),
										'table_price' => array(
														),
									),
						'google_prohibited' => 0,
					
						);
						
$pro_attach_metadata = '';					
$pro_attach_metadata = array(
						'width' => $width ,
						'height' => $height,
						'hwstring_small' => $hwstring_small,
						'file' => $file,
						'sizes' => 
									array(
										'thumbnail' => array(
														'file' => $imgtitle[0]."-150x150.".$imgtitle[1],
														'width' => 150,
														'height' => 150,
														),
										
									
										'medium' => array(
														'file' => $imgtitle[0]."-300x225.".$imgtitle[1],
														'width' => 300,
														'height' => 225,
														),
										
									
										'product-thumbnails' => array(
														'file' => $imgtitle[0]."-151x113.".$imgtitle[1],
														'width' => 151,
														'height' => 113,
														),
										
									
										'gold-thumbnails' => array(
														'file' => $imgtitle[0]."-31x23.".$imgtitle[1],
														'width' => 31,
														'height' => 23,
														),
										
								
										'admin-product-thumbnails' => array(
														'file' => $imgtitle[0]."-38x28.".$imgtitle[1],
														'width' => 38,
														'height' => 28,
														),
										
									
										'featured-product-thumbnails' => array(
														'file' => $imgtitle[0]."-286x215.".$imgtitle[1],
														'width' => 286,
														'height' => 215,
														),
										
									
										'small-product-thumbnail' => array(
														'file' => $imgtitle[0]."-151x113.".$imgtitle[1],
														'width' => 151,
														'height' => 113,
														),
										
									
										'medium-single-product' => array(
														'file' => $imgtitle[0]."-302x226.".$imgtitle[1],
														'width' => 'in',
														'height' => 'in',
														),
										
									
										'AWD_facebook_ogimage' => array(
														'file' => $imgtitle[0]."-200x200.".$imgtitle[1],
														'width' => 200,
														'height' => 200,
														),
										
									
										'wpsc-151x151' => array(
														'file' => $imgtitle[0]."-151x151.".$imgtitle[1],
														'width' => 151,
														'height' => 151,
														
										),							
											

									),
						'image_meta' => array(
										'aperture' => 0,
										'credit' => "",
										'camera' => "",
										'caption' => "",
										'created_timestamp' => 0,
										'copyright' => "",
										'focal_length' => 0,
										'iso' => 0,
										'shutter_speed' => 0,
										'title' => 0,
									),
						
					
						);
						
//echo "<pre>";print_r($pro_attach_metadata);		die;				
 $res1 = mysql_query("select * from wp_postmeta where meta_key = '_wpsc_sku' and meta_value = '".$sku."'");
 $row1 = mysql_fetch_array($res1);
 $post_id = $row1['post_id'];
 
 if($post_id == ""){



 $insert_id =  wp_insert_post( $my_post );
 $attachment_ID = wp_insert_attachment($attachment, $newpath, $insert_id);
 add_post_meta($insert_id, '_wpsc_sku', $sku);
 add_post_meta($insert_id, '_wpsc_price', $price);
 add_post_meta($insert_id, '_wpsc_special_price', $sale_price);
 add_post_meta($insert_id, '_wpsc_stock', $qty);
 add_post_meta($insert_id, '_wpsc_product_metadata', $pro_metadata);
 add_post_meta($attachment_ID, '_wp_attached_file', $file);
 update_post_meta($attachment_ID, '_wp_attachment_metadata', $pro_attach_metadata);
 $category = utf8_encode($cat);
 
 $rest = substr($category, 0, -1);
 $cat = explode("|",$rest);
  foreach($cat as $singlecat)
 {

   $subcat = explode(">",$singlecat);
   $i = 0;
 	foreach($subcat as $subitem){
		// echo $subitem."<br/>";
		 if($i == 0){
			$texonomy = wp_create_category($subitem,0);	
		 }else{
		 $j = $i - 1;
		    $texonomy = wp_create_category($subcat[$j],$subcat[$i]);	
		 }
 $texonomy = wp_create_category($singlecat,0);
 if($texonomy != ""){
 $update_texonomy = $texonomy;
  //echo 'update wp_term_taxonomy set taxonomy = "wpsc_product_category" where term_id = '.$update_texonomy;
 mysql_query('update wp_term_taxonomy set taxonomy = "wpsc_product_category", count = count+1 where term_id = '.$update_texonomy);
 }

 $res = mysql_query("select * from wp_term_taxonomy where term_id = ".$update_texonomy." order by term_taxonomy_id asc limit 1");
 $row = mysql_fetch_array($res);
 $tex_id = $row['term_taxonomy_id'];
 mysql_query('update wp_term_relationships set term_taxonomy_id ='.$tex_id.' where object_id = '.$insert_id);
$i++;	
	}
 }
 	
 
 }else{
  $my_post = array();
  $my_post['ID'] = $post_id;
  $my_post['post_title'] = trim($title,'"');
  $my_post['post_content'] = trim($con,'"');

 wp_update_post( $my_post );
 
 mysql_query('update wp_posts set post_title ="'.$imgtitle[0].'", post_name = "'.sanitize_title($imgtitle[0]).'", post_mime_type = "'.$type.'", guid ="'.$guid.'" where object_id = '.$insert_id);
 
  
 update_post_meta($post_id, '_wpsc_price', $price);
 update_post_meta($post_id, '_wpsc_special_price', $sale_price);
 update_post_meta($post_id, '_wpsc_stock', $qty);
 update_post_meta($post_id, '_wpsc_product_metadata', $pro_metadata);


 $category = utf8_encode($cat);
 $rest = substr($category, 0, -1);
 $cat = explode("|",$rest);
 foreach($cat as $singlecat)
 {

   $subcat = explode(">",$singlecat);
   
   if($subcat[1] == ""){
   
   				$texonomy = wp_create_category($singlecat,0);
				 if($texonomy != ""){
				 $update_texonomy = $texonomy;
				  //echo 'update wp_term_taxonomy set taxonomy = "wpsc_product_category" where term_id = '.$update_texonomy;
				 mysql_query('update wp_term_taxonomy set taxonomy = "wpsc_product_category", count = count+1 where term_id = '.$update_texonomy);
				 }
				
				 $res = mysql_query("select * from wp_term_taxonomy where term_id = ".$update_texonomy." order by term_taxonomy_id asc limit 1");
				 $row = mysql_fetch_array($res);
				 $tex_id = $row['term_taxonomy_id'];
				 mysql_query('update wp_term_relationships set term_taxonomy_id ='.$tex_id.' where object_id = '.$insert_id);
   
   }else{
				$i = 0;
				foreach($subcat as $subitem){
						 
						 if($i == 0){
							$texonomy = wp_create_category($subitem,0);	
						 }else{
						 //$j = $i - 1;
						 //echo $subitem."<br/>";
							echo $texonomy = wp_create_category($subitem,$texonomy);	

						 }
						 //echo $subcat[$j]."<br/>";
				 
				 if($texonomy != ""){
				 $update_texonomy = $texonomy;
				  //echo 'update wp_term_taxonomy set taxonomy = "wpsc_product_category" where term_id = '.$update_texonomy;
				 mysql_query('update wp_term_taxonomy set taxonomy = "wpsc_product_category", count = count+1 where term_id = '.$update_texonomy);
				 }
				
				 $res = mysql_query("select * from wp_term_taxonomy where term_id = ".$update_texonomy." order by term_taxonomy_id asc limit 1");
				 $row = mysql_fetch_array($res);
				 $tex_id = $row['term_taxonomy_id'];
				 mysql_query('update wp_term_relationships set term_taxonomy_id ='.$tex_id.' where object_id = '.$insert_id);
					$i++;	
					}
   }
 
 }
 

 
 
 

 }
 
$i++;
	}

@mysql_close($con); 

if($save) { 
    if(!is_writable($outputfile)) { 
        echo "File is not writable, check permissions.\n"; 
    } 
    else { 
        $file2 = fopen($outputfile,"w"); 
        if(!$file2) { 
            echo "Error writing to the output file.\n"; 
        } 
        else { 
            fwrite($file2,$queries); 
            fclose($file2); 
        } 
    } 
} 

echo "A total of $lines records in the csv file was imported successfully today.\n\n$queries";
$siteurl =  get_bloginfo('wpurl');
header("Location: ".$siteurl);
?>