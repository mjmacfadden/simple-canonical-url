<?php
/**
 * Plugin Name: Simple Canonical URL
 * Plugin URI: https://example.com
 * Description: Adds a canonical URL field to posts that can be edited in both the post editor and quick edit mode.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-canonical-url
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Simple_Canonical_URL {
    
    // Meta key for storing the canonical URL
    private $meta_key = '_canonical_url';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Add meta box to post edit screen
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        
        // Save meta box data
        add_action('save_post', array($this, 'save_meta_box'));
        
        // Add column to posts table
        add_filter('manage_posts_columns', array($this, 'add_posts_column'));
        add_action('manage_posts_custom_column', array($this, 'display_posts_column'), 10, 2);
        
        // Add field to quick edit
        add_action('quick_edit_custom_box', array($this, 'quick_edit_field'), 10, 2);
        
        // Add scripts for quick edit
        add_action('admin_footer', array($this, 'quick_edit_js'));
        
        // Save quick edit data
        add_action('save_post', array($this, 'save_quick_edit'));
        
        // Output canonical URL in frontend
        add_action('wp_head', array($this, 'output_canonical_url'), 1);
    }
    
    /**
     * Add meta box to post edit screen
     */
    public function add_meta_box() {
        add_meta_box(
            'canonical_url_meta_box',
            __('Canonical URL', 'simple-canonical-url'),
            array($this, 'display_meta_box'),
            array('post', 'page'),
            'normal',
            'high'
        );
    }
    
    /**
     * Display meta box content
     */
    public function display_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('canonical_url_meta_box', 'canonical_url_meta_box_nonce');
        
        // Get saved canonical URL
        $canonical_url = get_post_meta($post->ID, $this->meta_key, true);
        
        // Output field
        ?>
        <p>
            <label for="canonical_url_field"><?php _e('Enter the canonical URL for this post:', 'simple-canonical-url'); ?></label>
            <input type="url" class="large-text" id="canonical_url_field" name="canonical_url_field" value="<?php echo esc_url($canonical_url); ?>" placeholder="https://example.com/canonical-page/">
            <span class="description"><?php _e('Leave empty to use the post\'s permalink as the canonical URL.', 'simple-canonical-url'); ?></span>
        </p>
        <?php
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_box($post_id) {
        // Check if nonce is set
        if (!isset($_POST['canonical_url_meta_box_nonce'])) {
            return;
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['canonical_url_meta_box_nonce'], 'canonical_url_meta_box')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save the canonical URL if provided
        if (isset($_POST['canonical_url_field'])) {
            $canonical_url = esc_url_raw($_POST['canonical_url_field']);
            update_post_meta($post_id, $this->meta_key, $canonical_url);
        }
    }
    
    /**
     * Add column to posts table
     */
    public function add_posts_column($columns) {
        $columns['canonical_url'] = __('Canonical URL', 'simple-canonical-url');
        return $columns;
    }
    
    /**
     * Display custom column content
     */
    public function display_posts_column($column, $post_id) {
        if ($column === 'canonical_url') {
            $canonical_url = get_post_meta($post_id, $this->meta_key, true);
            if (!empty($canonical_url)) {
                echo '<span class="canonical-url-value">' . esc_url($canonical_url) . '</span>';
            } else {
                echo '<span class="canonical-url-value">—</span>';
            }
        }
    }
    
    /**
     * Add field to quick edit
     */
    public function quick_edit_field($column_name, $post_type) {
        if ($column_name !== 'canonical_url') {
            return;
        }
        
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label class="inline-edit-canonical-url-wrap">
                    <span class="title"><?php _e('Canonical URL', 'simple-canonical-url'); ?></span>
                    <span class="input-text-wrap">
                        <input type="url" name="canonical_url_field" class="canonical_url_field" value="">
                    </span>
                </label>
            </div>
        </fieldset>
        <?php
    }
    
    /**
     * Add JavaScript for quick edit
     */
    public function quick_edit_js() {
        $current_screen = get_current_screen();
        
        // Only add to post listing screen
        if (!$current_screen || $current_screen->base !== 'edit') {
            return;
        }
        
        ?>
        <script type="text/javascript">
        jQuery(function($) {
            // Save the original quick edit function
            var $wp_inline_edit = inlineEditPost.edit;
            
            // Override the quick edit function
            inlineEditPost.edit = function(id) {
                // Run the original function
                $wp_inline_edit.apply(this, arguments);
                
                // Get the post ID
                var post_id = 0;
                if (typeof(id) === 'object') {
                    post_id = parseInt(this.getId(id));
                }
                
                if (post_id > 0) {
                    // Get the row with the post
                    var $row = $('#post-' + post_id);
                    
                    // Get the canonical URL
                    var canonical_url = $row.find('.canonical-url-value').text().trim();
                    
                    // Set the value in the quick edit field
                    $('#edit-' + post_id).find('input.canonical_url_field').val(canonical_url === '—' ? '' : canonical_url);
                }
            };
        });
        </script>
        <?php
    }
    
    /**
     * Save quick edit data
     */
    public function save_quick_edit($post_id) {
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Check if quick edit
        if (!isset($_POST['_inline_edit'])) {
            return;
        }
        
        // Save the canonical URL if provided in quick edit
        if (isset($_POST['canonical_url_field'])) {
            $canonical_url = esc_url_raw($_POST['canonical_url_field']);
            update_post_meta($post_id, $this->meta_key, $canonical_url);
        }
    }
    
    /**
     * Output canonical URL in the frontend
     */
    public function output_canonical_url() {
        if (is_singular()) {
            $post_id = get_queried_object_id();
            $canonical_url = get_post_meta($post_id, $this->meta_key, true);
            
            if (!empty($canonical_url)) {
                echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
                
                // Remove WordPress default canonical
                remove_action('wp_head', 'rel_canonical');
            }
        }
    }
}

// Initialize the plugin
new Simple_Canonical_URL();