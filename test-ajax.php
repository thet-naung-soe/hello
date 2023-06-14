<?php 
get_header(); 
$qry_obj = get_queried_object();
$obj_id = $qry_obj->term_id;
$obj_slug = $qry_obj->slug;
$obj_name = $qry_obj->name;
get_template_part('partials/page-cover-banner');

$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode("/", $uri_path);
$uri_segments_2 = $uri_segments[2];  

$current_term = get_term($obj_id, PL_TAXO_TYPE);

?>
<section class="inside-pg listing-pg">
    <div class="custom-block">
        <div class="row collection-container">
            <?php if ($current_term->description) { ?>
                <div class="col-lg-3 col-md-6">
                    <h3 class="collection-concept">Our Concept</h3>
                    <p><?php echo $current_term->description; ?></p>
                </div>
            <?php } ?>
            <?php
            $collections = get_posts(array(
                'post_type' => PL_PT_PRODUCT,
                'post_status' => 'publish',
                'numberposts' => 3,
                'tax_query' => array(
                    array(
                        'taxonomy' => PL_TAXO_COLLECTION,
                        'field' => 'slug',
                        'terms' => $obj_slug,
                        'include_children' => false
                    )
                )
            ));
                    
            if ($collections) {
                foreach ($collections as $collection) {
                    $collection_title = $collection->post_title;
                    $collection_link = get_permalink($collection->ID);
                    $collection_img = wp_get_attachment_image_src(get_post_thumbnail_id($collection->ID),'full');
                    $collection_img = aq_resize($collection_img[0], 'null', 317, false, true, true); 
            ?>
                    <div class="col-lg-3 col-md-6 item-box">
                        <figure class="snip1458 text-center">
                            <img src="<?php echo $collection_img; ?>" alt="<?php echo $collection_title; ?>" class="img-fluid">
                            <figcaption>
                                <h3><?php echo $collection_title; ?></h3>
                            </figcaption>
                            <i class="fas fa-couch"></i>
                            <a href="<?php echo $collection_link; ?>"></a>
                        </figure>
                    </div>
            <?php } } ?> 
        </div>
    </div>
        
    <div class="brand-container">
        <div class="custom-block">
            <?php
            $brand_taxos = get_terms(PL_TAXO_BRAND, 
                array(
                    'parent' => 0, 
                    'hide_empty'=>'false'
                )
            );
            ?>
            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                <?php 
                $tax_order = 0;
                foreach ($brand_taxos as $b_key => $brand) { 
                    $term_title = $brand->name;
                    $term_id = $brand->term_id;
                    $term_link = get_term_link($brand, PL_TAXO_BRAND );
                    $brand_logo = get_field('logo', PL_TAXO_BRAND . '_' . $brand->term_id);
                ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($term_id == 0) ? 'active' : ''; ?>" id="<?php echo '_pl_'.$b_key; ?>" data-id="<?php echo $brand->term_id; ?>" data-toggle="tab" href="#<?php echo 'tab_'.$b_key; ?>" role="tab" aria-controls="<?php echo 'tab_'.$b_key; ?>" aria-selected="true">
                            <img src="<?php echo $brand_logo; ?>" alt="<?php echo $term_title; ?>" class="img-fluid">
                        </a>
                    </li>
                    <script>
                    $(document).ready(function () {
                        $(document).ajaxStart(function () {
                            $("#loading").show();
                        }).ajaxStop(function () {
                            $("#loading").hide();
                        });

                        $('#<?php echo '_pl_'.$b_key; ?>').click(function(){
                            var term_id = $('#<?php echo '_pl_'.$b_key; ?>').data("id");
                            $.ajax({
                                type: "POST", 
                                url: "/wp-admin/admin-ajax.php", 
                                data: 'term_id=' + term_id + '&action=get_term_products',
                                dataType: "json",
                                success: function(data) {                
                                    if (data.status == 'yes') {
                                        $("#replace-container").html(data.html); 

                                        $('html, body').animate({ // Smooth scroll to div id
                                            scrollTop: $("#replace-container").offset().top
                                        }, 700);                 
                                    }
                                    else {
                                    }
                                },
                                error: function() {
                                }
                            });      
                        });
                    });
                    </script>
                <?php 
                    $tax_order++; 
                } 
                ?>
                <!-- Loading for AJAX Processing -->
                <div id="loading"><img src="<?php echo ASSET_URL.'images/loading.gif'; ?>"></div>
                <!-- Loading for AJAX Processing -->
            </ul>
        </div>
    </div>

    <div class="custom-block product-container">
        <!-- Replace data to taxonomy -->
        <div id="replace-container">
            <!-- Products of Current Post Type -->
            <?php
            $current_products = get_posts(array(
                'post_type' => PL_PT_PRODUCT,
                'post_status' => 'publish',
                'numberposts' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => PL_TAXO_TYPE,
                        'field' => 'slug',
                        'terms' => $obj_slug,
                        'include_children' => false
                    )
                )
            ));
            if ($current_products) {
            ?>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active"  role="tabpanel">
                        <p class="section-title text-center">EXPERIENCE DESIGN AND TECHNOLOGY</p>
                        <p class="section-sub-title text-center"><span><?php echo $obj_name; ?></span></p>
                        <div class="row product-items">
                            <?php 
                            foreach ($current_products as $p_xl_key => $product) {
                                    $product_link = get_permalink($product->ID);
                                    $product_img = wp_get_attachment_image_src(get_post_thumbnail_id($product->ID), 'full');
                                    $product_img = aq_resize($product_img[0], 'null', 317, false, true, true); 
                                    $fields = get_fields($product->ID);
                                    $item_code = $fields['item_code'];
                            ?>
                                    <article class="col-md-6 col-lg-4 card item-box">
                                        <?php if ($product_img) { ?>
                                            <div class="item">
                                                <a href="<?php echo $product_link; ?>">
                                                    <figure><img src="<?php echo WP_HOME.$product_img; ?>" alt="<?php echo $product->post_title; ?>" class="img-fluid"></figure>
                                                    <?php if ($product->post_title) { ?> 
                                                        <div class="item_content">
                                                            <div class="info-box">
                                                                <label><?php echo $product->post_title; ?></label>
                                                                <h3><?php echo $item_code; ?></h3>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </a>
                                            </div>
                                        <?php } ?>
                                        <div class="card-body">    
                                            <?php if ($product->post_title) { ?>  
                                                <h3><a href="<?php echo $product_link; ?>"><?php echo $product->post_title; ?></a></h3>
                                            <?php } ?>
                                            <?php if ($item_code) { ?> 
                                                <p><?php echo $item_code; ?></p>
                                            <?php } ?>
                                        </div>
                                    </article>
                            <?php } ?>
                        </div>
                    </div>
                </div>  
            <?php } ?>        
            <!-- #Products of Current Post Type -->
        </div>
        <!-- Replace data to taxonomy -->
    </div>
</section>
<?php 
// endwhile; endif;
get_footer(); 
?>