<?php

namespace BroDudeProfile\Classes;


use BroDudeProfile;

class Router
{

    private $slug;

    public function __construct()
    {
        $this->slug = BroDudeProfile::$slug;

        add_action('init', [$this, 'router']);

        add_action("template_redirect", function () {
            $this->redirect();
        });

    }

    public function redirect()
    {

        if (get_query_var('pagename') === BroDudeProfile::$slug) {
            status_header(200);
        }
        $redirect = new Redirect();
        $redirect->redirect_profile();
        $redirect->not_exist_user();
        $redirect->self_profile();
    }

    public function router()
    {
        $slug = BroDudeProfile::$slug;

        add_rewrite_tag('%tab_active%', '([^&]+)');
        add_rewrite_tag('%uid%', '([^&]+)');

        add_rewrite_rule(
            "^({$this->slug})\/([A-z]*)\/([0-9]+)\/{0,}($|\/page\/?([0-9]{1,})\/?$)",
            'index.php?pagename=' . $slug . '&tab_active=$matches[2]&uid=$matches[3]&paged=$matches[5]',
            'bottom'
        );

        if (is_user_logged_in()) {
            add_rewrite_rule(
                "^({$this->slug})\/([A-z]*)\/{0,}($|\/page\/?([0-9]{1,})\/?$)",
                'index.php?pagename=' . $slug . '&tab_active=$matches[2]&paged=$matches[4]',
                'bottom'
            );
        }
    }

}