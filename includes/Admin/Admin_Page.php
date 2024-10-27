<?php

namespace AIBP\Admin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Admin_Page {

    public $parent = null;
    public $capability = 'manage_options';
    public $icon = 'dashicons-welcome-add-page';
    public $position = 2;
    public $id = 'aibp';
    public $page_title;
    public $menu_title;

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    public function admin_menu() {
        if( ! $this->parent ) {
            add_menu_page(
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->id,
                [$this, 'display'],
                $this->icon,
                $this->position
            );
        }
        else {
            add_submenu_page(
                $this->parent,
                $this->page_title,
                $this->menu_title,
                $this->capability,
                $this->id,
                [$this, 'display']
            );
        }
    }

    public function display() {
        echo 'page_content';
    }
}
