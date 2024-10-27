<?php

namespace AIBP\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

use AIBP\Admin\Admin_Page;
use AIBP\Helper\Helper;

class Admin_Page_Dashboard extends Admin_Page {

    public function __construct() {
        $this->page_title = esc_html__( 'AI Bulk Post', 'ai-bulk-post' );
        $this->menu_title = esc_html__( 'AI Bulk Post', 'ai-bulk-post' );
        
        parent::__construct();
    }

    public function display() {
        ?>
            <div class="wrap">
                <h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
                <a href="#" class="page-title-action aibp-add-new-rule"><?php esc_html_e( 'Add New Rule', 'ai-bulk-post' ); ?></a>
                <hr class="wp-header-end">
                <div class="ai-bulk-post--events">
                    <?php Helper::get_events_table(); ?>
                </div>
            </div>
        <?php
    }

}
