<?php /* Template Name: userpage */ ?>

<?php get_header(); ?>

<div class="si-container">

	<div id="primary" class="content-area">

		<main id="content" class="site-content" role="main"<?php sinatra_schema_markup( 'main' ); ?>>

            <?php 
            if(is_user_logged_in()){
                $current_user = wp_get_current_user();
                global $wpdb;
                $entry_ids = $wpdb->get_results( "SELECT entry_id FROM sk8_gf_entry_meta WHERE meta_value = '$current_user->user_email'" );
                $pdf_info = array();
              
                echo "<h2> Hello $current_user->display_name! Here are the certificates you have been awarded:</h2>"; ?>
                </br>
                <?php
                
                foreach ($entry_ids as $value){
                    $date = $wpdb->get_var("SELECT date_created FROM sk8_gf_entry_notes WHERE entry_id = $value->entry_id");
                    $pdfs = GPDFAPI::get_entry_pdfs($value->entry_id);
                    $date = substr($date, 0, 10);
                    $date_obj = new DateTime($date);
                    $date_formatted = $date_obj->format("m/d/Y");
                    foreach ($pdfs as $pdf){
                        $pdf_info[$value->entry_id] = array("pdf" => $pdf['id'], "date" => $date_formatted, "title"=>$pdf['name'], "entry_id"=>$value->entry_id);
                    }
                }

                ?>
            <table>
                <tr>    
                    <th>Date Awarded</th>
                    <th>Title</th>
                    <th>Certificates</th>
                </tr>
            <?php

            foreach($pdf_info as $entry){
                echo "<tr>";
                echo "<td>";
                echo $entry['date'];
                echo "</td>";
                echo "<td>";
                echo $entry['title'];
                echo "</td>";
                echo "<td>";
                $id = $entry['pdf'];
                $entry_id = $entry['entry_id'];
                echo do_shortcode("[gravitypdf id='$id' entry='$entry_id']");
                echo "</td>";
                echo "</tr>";
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