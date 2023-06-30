<?php /* Template Name: userpage */ ?>

<?php get_header(); ?>

<div class="si-container">

	<div id="primary" class="content-area">

		<main id="content" class="site-content" role="main"<?php sinatra_schema_markup( 'main' ); ?>>

            <?php 
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                global $wpdb;
                $form_ids = $wpdb->get_results( "SELECT form_id FROM sk8_gf_entry_meta WHERE meta_value = '$current_user->user_email'" );
                $entry_ids = $wpdb->get_results( "SELECT entry_id FROM sk8_gf_entry_meta WHERE meta_value = '$current_user->user_email'" );
                $titles = array();
                $urls = array();
                $confirmations = array();
                $dates = array();
                $pdf_ids = array();
                /*
                echo '<pre>'; print_r($entry_ids); echo '</pre>';
                $pdfs = GPDFAPI::get_entry_pdfs($entry_ids[0]->entry_id);
                echo '<pre>'; print_r($pdfs); echo '</pre>';
                foreach ($pdfs as $pdf){
                    echo $pdf['id'] ;
                }*/
                echo "<h2> Hello $current_user->display_name! Here are the certificates you have been awarded:</h2>"; ?>
                </br>
                <?php
                foreach ($form_ids as $value){
                    $title = $wpdb->get_var("SELECT title FROM sk8_gf_form WHERE id = $value->form_id");
                    array_push($titles, $title);
                    /*
                    Use this code to get form URLs should it be needed

                    $url = $wpdb->get_var("SELECT source_url FROM sk8_gf_entry WHERE form_id = $value->form_id AND source_url REGEXP 'page_id'");
                    array_push($urls, $url);
                    */
                    $confirmation = $wpdb->get_var("SELECT confirmations FROM sk8_gf_form_meta WHERE form_id = $value->form_id");
                    array_push($confirmations, $confirmation);
                }
                foreach ($entry_ids as $value){
                    $date = $wpdb->get_var("SELECT date_created FROM sk8_gf_entry_notes WHERE entry_id = $value->entry_id");
                    $pdfs = GPDFAPI::get_entry_pdfs($value->entry_id);
                    $date = substr($date, 0, 10);
                    $date_obj = new DateTime($date);
                    $date_formatted = $date_obj->format("m/d/Y");
                    array_push($dates, $date_formatted);
                    foreach ($pdfs as $pdf){
                        array_push($pdf_ids, $pdf['id']);
                    }
                }
                /*
                SQL + Regex way of obtaining pdf_ids. Doesn't work for conditionals. 
                $pdf_ids = array();
                foreach ($confirmations as $value){
                    $obj = json_decode($value, true);
                    $id_key = key($obj);
                    reset($obj);
                    $message = $obj[$id_key]['message'];
                    preg_match_all('/id="([^"]+)"/', $message, $matches);
                    $ids = $matches[1];
                    array_push($pdf_ids, $ids);
                }
                */
                ?>
            <table>
                <tr>    
                    <th>Date Awarded</th>
                    <th>Title</th>
                    <th>Certificates</th>
                </tr>
            <?php
                for($x=0; $x<count($titles); $x++){
                    if($titles[$x] != "UserRegistrationForm"){
                        echo "<tr>
                        <td>$dates[$x]</td>
                        <td>$titles[$x]</td>";
                        $obj = $entry_ids[$x];
                        $pdf_id = $pdf_ids[$x];
                        echo "<td>";
                        echo do_shortcode("[gravitypdf id='$pdf_id' entry='$obj->entry_id']");
                        echo "</td>";
                        /*
                        $row = $pdf_ids[$x];
                        for($j = 0; $j<count($row); $j++){
                            $pdf_id = $row[$j];
                            //$pdfs = GPDFAPI::get_entry_pdfs($obj->entry_id);
                            echo "<td>";
                            echo do_shortcode("[gravitypdf id='$pdf_id' entry='$obj->entry_id']");
                            echo $pdf_id;
                            echo "</td>";
                        }*/
                        echo "</tr>";
                    }
                }
                ?>
            </table>
            <?php
             }else{
                echo "<h2>Please login to see the certificates you've been awarded</h2>";
            }
            
            
            ?>

			<?php
			do_action( 'sinatra_before_singular' );

			do_action( 'sinatra_content_singular' );

			do_action( 'sinatra_after_singular' );
			?>

		</main><!-- #content .site-content -->

		<?php do_action( 'sinatra_after_content' ); ?>

	</div><!-- #primary .content-area -->

</div><!-- END .si-container -->

<?php
get_footer();