      <footer>
            <?php 
              $args = array(
                'theme_location' => 'header-menu',
                'container' => 'nav',
                'after' => '<span class="separator"> | </span>'
              );
              wp_nav_menu($args);
            ?>
            <div class="location">
              <p><?php echo esc_html(get_option('lapizzeria_location')); ?></p>
              <p>Phone Number: <?php echo esc_html(get_option('lapizzeria_phonenumber')); ?></p>
            </div>
            <p class="copyright">All rights reserved <?php echo date('Y'); ?></p>
      </footer>
      <?php wp_footer(); ?>
    </body>
</html>