<?php

function remove_page_parent_post_tye( $classes, $item ) {
    if( ( is_post_type_archive( 'specialties' ) || is_singular( 'specialties' ) )
        && $item->title == 'Blog' ){
        $classes = array_diff( $classes, array( 'current_page_parent' ) );
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'remove_page_parent_post_tye', 10, 2 );





function dimox_breadcrumbs() {

	/* === OPTIONS === */
	$text['home']     = 'Home'; // text for the 'Home' link
	$text['category'] = 'Archive by Category "%s"'; // text for a category page
	$text['search']   = 'Search Results for "%s" Query'; // text for a search results page
	$text['tag']      = 'Posts Tagged "%s"'; // text for a tag page
	$text['author']   = 'Articles Posted by %s'; // text for an author page
	$text['404']      = 'Error 404'; // text for the 404 page
	$text['page']     = 'Page %s'; // text 'Page N'
	$text['cpage']    = 'Comment Page %s'; // text 'Comment Page N'

	$wrap_before    = '<div class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">'; // the opening wrapper tag
	$wrap_after     = '</div><!-- .breadcrumbs -->'; // the closing wrapper tag
	$sep            = '›'; // separator between crumbs
	$sep_before     = '<span class="sep">'; // tag before separator
	$sep_after      = '</span>'; // tag after separator
	$show_home_link = 1; // 1 - show the 'Home' link, 0 - don't show
	$show_on_home   = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
	$show_current   = 1; // 1 - show current page title, 0 - don't show
	$before         = '<span class="current">'; // tag before the current crumb
	$after          = '</span>'; // tag after the current crumb
	/* === END OF OPTIONS === */

	global $post;
	$home_url       = home_url('/');
	$link_before    = '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
	$link_after     = '</span>';
	$link_attr      = ' itemprop="item"';
	$link_in_before = '<span itemprop="name">';
	$link_in_after  = '</span>';
	$link           = $link_before . '<a href="%1$s"' . $link_attr . '>' . $link_in_before . '%2$s' . $link_in_after . '</a>' . $link_after;
	$frontpage_id   = get_option('page_on_front');
	$parent_id      = ($post) ? $post->post_parent : '';
	$sep            = ' ' . $sep_before . $sep . $sep_after . ' ';
	$home_link      = $link_before . '<a href="' . $home_url . '"' . $link_attr . ' class="home">' . $link_in_before . $text['home'] . $link_in_after . '</a>' . $link_after;

	if (is_home() || is_front_page()) {

		if ($show_on_home) echo $wrap_before . $home_link . $wrap_after;

	} else {

		echo $wrap_before;
		if ($show_home_link) echo $home_link;

		if ( is_category() ) {
			$cat = get_category(get_query_var('cat'), false);
			if ($cat->parent != 0) {
				$cats = get_category_parents($cat->parent, TRUE, $sep);
				$cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
				$cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
				if ($show_home_link) echo $sep;
				echo $cats;
			}
			if ( get_query_var('paged') ) {
				$cat = $cat->cat_ID;
				echo $sep . sprintf($link, get_category_link($cat), get_cat_name($cat)) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
			} else {
				if ($show_current) echo $sep . $before . sprintf($text['category'], single_cat_title('', false)) . $after;
			}

		} elseif ( is_search() ) {
			if (have_posts()) {
				if ($show_home_link && $show_current) echo $sep;
				if ($show_current) echo $before . sprintf($text['search'], get_search_query()) . $after;
			} else {
				if ($show_home_link) echo $sep;
				echo $before . sprintf($text['search'], get_search_query()) . $after;
			}

		} elseif ( is_day() ) {
			if ($show_home_link) echo $sep;
			echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $sep;
			echo sprintf($link, get_month_link(get_the_time('Y'), get_the_time('m')), get_the_time('F'));
			if ($show_current) echo $sep . $before . get_the_time('d') . $after;

		} elseif ( is_month() ) {
			if ($show_home_link) echo $sep;
			echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y'));
			if ($show_current) echo $sep . $before . get_the_time('F') . $after;

		} elseif ( is_year() ) {
			if ($show_home_link && $show_current) echo $sep;
			if ($show_current) echo $before . get_the_time('Y') . $after;

		} elseif ( is_single() && !is_attachment() ) {
			if ($show_home_link) echo $sep;
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				printf($link, $home_url . $slug['slug'] . '/', $post_type->labels->singular_name);
				if ($show_current) echo $sep . $before . get_the_title() . $after;
			} else {
				$cat = get_the_category(); $cat = $cat[0];
				$cats = get_category_parents($cat, TRUE, $sep);
				if (!$show_current || get_query_var('cpage')) $cats = preg_replace("#^(.+)$sep$#", "$1", $cats);
				$cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
				$cat = get_the_category();

				echo $cats;
				if ( get_query_var('cpage') ) {
					echo $sep . sprintf($link, get_permalink(), get_the_title()) . $sep . $before . sprintf($text['cpage'], get_query_var('cpage')) . $after;
				} else {
					if ($show_current) echo $before . get_the_title() . $after;
				}
			}

		// custom post type
		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			$post_type = get_post_type_object(get_post_type());
			if ( get_query_var('paged') ) {
				echo $sep . sprintf($link, get_post_type_archive_link($post_type->name), $post_type->label) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
			} else {
				// if ($show_current) echo $sep . $before . $post_type->label . $after;
			}

		} elseif ( is_attachment() ) {
			if ($show_home_link) echo $sep;
			$parent = get_post($parent_id);
			$cat = get_the_category($parent->ID); $cat = $cat[0];
			if ($cat) {
				$cats = get_category_parents($cat, TRUE, $sep);
				$cats = preg_replace('#<a([^>]+)>([^<]+)<\/a>#', $link_before . '<a$1' . $link_attr .'>' . $link_in_before . '$2' . $link_in_after .'</a>' . $link_after, $cats);
				echo $cats;
			}
			printf($link, get_permalink($parent), $parent->post_title);
			if ($show_current) echo $sep . $before . get_the_title() . $after;

		} elseif ( is_page() && !$parent_id ) {
			if ($show_current) echo $sep . $before . get_the_title() . $after;

		} elseif ( is_page() && $parent_id ) {
			if ($show_home_link) echo $sep;
			if ($parent_id != $frontpage_id) {
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					if ($parent_id != $frontpage_id) {
						$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					}
					$parent_id = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) echo $sep;
				}
			}
			if ($show_current) echo $sep . $before . get_the_title() . $after;

		} elseif ( is_tag() ) {
			if ( get_query_var('paged') ) {
				$tag_id = get_queried_object_id();
				$tag = get_tag($tag_id);
				echo $sep . sprintf($link, get_tag_link($tag_id), $tag->name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
			} else {
				if ($show_current) echo $sep . $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
			}

		} elseif ( is_author() ) {
			global $author;
			$author = get_userdata($author);
			if ( get_query_var('paged') ) {
				if ($show_home_link) echo $sep;
				echo sprintf($link, get_author_posts_url($author->ID), $author->display_name) . $sep . $before . sprintf($text['page'], get_query_var('paged')) . $after;
			} else {
				if ($show_home_link && $show_current) echo $sep;
				if ($show_current) echo $before . sprintf($text['author'], $author->display_name) . $after;
			}

		} elseif ( is_404() ) {
			if ($show_home_link && $show_current) echo $sep;
			if ($show_current) echo $before . $text['404'] . $after;

		} elseif ( has_post_format() && !is_singular() ) {
			if ($show_home_link) echo $sep;
			echo get_post_format_string( get_post_format() );
		}

		echo $wrap_after;

	}
}



function caja_nosotros($imagen, $descripcion ) { ?>
	<div class="caja">
		<?php
		$id_imagen = get_field($imagen);
		$imagen = wp_get_attachment_image_src($id_imagen, 'nosotros');
		?>
		<img src="<?php echo $imagen[0] ?>" class="imagen-caja">
		<div class="contenido-caja">
			<?php the_field($descripcion); ?>
		</div>
	</div><!--.caja-->
<?php
}


function lapizzeria_agregar_recaptcha() { ?>
    <script src='https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit'  async defer></script>
    <script>
    var CaptchaCallback = function() {
      $('.g-recaptcha').each(function(index, el) {
        grecaptcha.render(el, {'sitekey' : $(el).data('sitekey'), 'expired-callback': expCallback});
      });
    };
    var expCallback = function() {
        grecaptcha.reset();
     };

    </script>
<?php
}
add_action('wp_head', 'lapizzeria_agregar_recaptcha');

// Tablas personalizadas y otras funciones
require get_template_directory() . '/inc/database.php';

// Funciones para las reservaciones
require get_template_directory() . '/inc/reservaciones.php';

// Crear opciones para el Template
require get_template_directory() . '/inc/opciones.php';

function lapizzeria_setup() {

    add_theme_support('post-thumbnails');

    add_image_size('nosotros', 437, 291, true);

    add_image_size('especialidades', 768, 515, true);

    add_image_size('especialidades_portrait', 435, 526, true);

    update_option('thumbnail_size_w', 253);
    update_option('thumbnail_size_h', 164);
}
add_action('after_setup_theme', 'lapizzeria_setup');

function lapizzeria_styles() {

    // Rgistrar los estilos
    wp_register_style('normalize', get_template_directory_uri() . '/css/normalize.css', array(), '5.0.0' );
    wp_register_style('google_yes', 'https://fonts.googleapis.com/css?family=Open+Sans|Raleway:400,700,900', array(), '1.0.0');
    wp_register_style('fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css', array('normalize'), '4.7.0' );
    wp_register_style('fluidboxcss', get_template_directory_uri() . '/css/fluidbox.min.css', array('normalize'), '4.7.0' );
    wp_register_style('datetime-local', get_template_directory_uri() . '/css/datetime-local-polyfill.css', array('normalize'), '4.7.0' );
    wp_register_style('style', get_template_directory_uri() . '/style.css', array('normalize'), '1.0' );

    //Llamar a los estilos
    wp_enqueue_style('normalize');
    wp_enqueue_style('fontawesome');
    wp_enqueue_style('fluidboxcss');
    wp_enqueue_style('datetime-local');
    wp_enqueue_style('style');

    // Registrar JS

    $apikey = esc_html(get_option('lapizzeria_gmap_apikey'));
    wp_register_script('maps', 'https://maps.googleapis.com/maps/api/js?key=' . $apikey . '&callback=initMap', array(), '', true);
    wp_register_script('fluidbox', get_template_directory_uri() . '/js/jquery.fluidbox.min.js', array(), '1.0.0', true  );
    wp_register_script('datetime-local-polyfill', get_template_directory_uri() . '/js/datetime-local-polyfill.min.js', array(), '1.0.0', true  );
    wp_register_script('modernizr', 'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js', array(), '2.8.3', true);
    wp_register_script('scripts', get_template_directory_uri() . '/js/scripts.js', array(), '1.0.0', true  );


    wp_enqueue_script('maps');
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_script('datetime-local-polyfill');
    wp_enqueue_script('modernizr');
    wp_enqueue_script('fluidbox');
    wp_enqueue_script('scripts');


    // Pasar variables de PHP a JavaScript.
    wp_localize_script(
      'scripts',
      'opciones',
      array(
          'latitud' => get_option('lapizzeria_gmap_latitud'),
          'longitud' => get_option('lapizzeria_gmap_longitud'),
          'zoom' => get_option('lapizzeria_gmap_zoom'),
      )
    );
}

add_action('wp_enqueue_scripts', 'lapizzeria_styles');

function lapizzeria_admin_scripts() {
	wp_enqueue_style('sweetalertcss', get_template_directory_uri() . '/css/sweetalert2.min.css');
	wp_enqueue_script('sweetalert', get_template_directory_uri() . '/js/sweetalert2.min.js');
	wp_enqueue_script('adminjs', get_template_directory_uri() . '/js/admin_ajax.js', array('jquery'), 1.0, true );
	wp_enqueue_script('promisesjs', 'https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js', array('sweetalert'), 1.0, true );
	wp_localize_script(
		'adminjs',
		'url_eliminar',
		array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) )
	  );
}
add_action('admin_enqueue_scripts', 'lapizzeria_admin_scripts');

// Agregar Async y Defer
function agregar_async_defer($tag, $handle){
  if('maps' !== $handle)
    return $tag;
  return str_replace(' src', ' async="async" defer="defer" src', $tag);
}
add_filter('script_loader_tag', 'agregar_async_defer', 10, 2 );

// Creación de menus
function lapizzeria_menus(){
    register_nav_menus(array(
        'header-menu' => __('Header Menu', 'lapizzeria'),
        'social-menu' => __('Social Menu', 'lapizzeria')
     ));
}
add_action('init', 'lapizzeria_menus');


add_action( 'init', 'lapizzeria_especialidades' );
function lapizzeria_especialidades() {
	$labels = array(
		'name'               => _x( 'Pizzas', 'lapizzeria' ),
		'singular_name'      => _x( 'Pizzas', 'post type singular name', 'lapizzeria' ),
		'menu_name'          => _x( 'Pizzas', 'admin menu', 'lapizzeria' ),
		'name_admin_bar'     => _x( 'Pizzas', 'add new on admin bar', 'lapizzeria' ),
		'add_new'            => _x( 'Add New', 'book', 'lapizzeria' ),
		'add_new_item'       => __( 'Add New Pizza', 'lapizzeria' ),
		'new_item'           => __( 'New Pizzas', 'lapizzeria' ),
		'edit_item'          => __( 'Edit Pizzas', 'lapizzeria' ),
		'view_item'          => __( 'View Pizzas', 'lapizzeria' ),
		'all_items'          => __( 'All Pizzas', 'lapizzeria' ),
		'search_items'       => __( 'Search Pizzas', 'lapizzeria' ),
		'parent_item_colon'  => __( 'Parent Pizzas:', 'lapizzeria' ),
		'not_found'          => __( 'No Pizzases found.', 'lapizzeria' ),
		'not_found_in_trash' => __( 'No Pizzases found in Trash.', 'lapizzeria' )
	);

	$args = array(
		'labels'             => $labels,
    'description'        => __( 'Description.', 'lapizzeria' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'especialidades' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 6,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
    'taxonomies'          => array( 'category' ),
	);

	register_post_type( 'especialidades', $args );
}

//Widgets

function lapizzeria_widgets(){
    register_sidebar( array(
        'name'    =>  'Blog Sidebar',
        'id'      =>  'blog_sidebar',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ));
}
add_action('widgets_init', 'lapizzeria_widgets');

/** ADVANCED CUSTOM FIELDS**/


define( 'ACF_LITE', true );


include_once('advanced-custom-fields/acf.php');

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_especialidades',
		'title' => 'Especialidades',
		'fields' => array (
			array (
				'key' => 'field_58ba4dacdfa15',
				'label' => 'Precio',
				'name' => 'precio',
				'type' => 'text',
				'instructions' => 'Añada un precio del platillo',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'especialidades',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_inicio',
		'title' => 'Inicio',
		'fields' => array (
			array (
				'key' => 'field_58c4cafccd84d',
				'label' => 'Contenido',
				'name' => 'contenido',
				'type' => 'wysiwyg',
				'instructions' => 'Agregue la descripción',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
			array (
				'key' => 'field_58c4cb0acd84e',
				'label' => 'Imagen',
				'name' => 'imagen',
				'type' => 'image',
				'instructions' => 'Agregue la imagen',
				'save_format' => 'url',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page',
					'operator' => '==',
					'value' => '5',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
	register_field_group(array (
		'id' => 'acf_sobre-nosotros',
		'title' => 'Sobre Nosotros',
		'fields' => array (
			array (
				'key' => 'field_58ba3fde05110',
				'label' => 'Imagen 1',
				'name' => 'imagen_1',
				'type' => 'image',
				'instructions' => 'Suba una imagen',
				'save_format' => 'id',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_58ba403c05113',
				'label' => 'Descripcion 1',
				'name' => 'descripcion_1',
				'type' => 'wysiwyg',
				'instructions' => 'Agrega aquí la descripción',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
			array (
				'key' => 'field_58ba402d05111',
				'label' => 'Imagen 2',
				'name' => 'imagen_2',
				'type' => 'image',
				'instructions' => 'Suba una imagen',
				'save_format' => 'id',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_58ba406605114',
				'label' => 'Descripcion 2',
				'name' => 'descripcion_2',
				'type' => 'wysiwyg',
				'instructions' => 'Agrega aquí la descripción',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
			array (
				'key' => 'field_58ba403305112',
				'label' => 'Imagen 3',
				'name' => 'imagen_3',
				'type' => 'image',
				'instructions' => 'Suba una imagen',
				'save_format' => 'id',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_58ba407005115',
				'label' => 'Descripcion 3',
				'name' => 'descripcion_3',
				'type' => 'wysiwyg',
				'instructions' => 'Agrega aquí la descripción',
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page',
					'operator' => '==',
					'value' => '9',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
