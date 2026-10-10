<?php

/**
 * Sidebar menu of the admin panel.
 *
 * An item is shown only when its route exists, so you can list future pages here
 * and they appear automatically once you add the route.
 *
 *  ['heading' => 'Text']                       = a section title
 *  ['label', 'route', 'icon', 'match']         = a link ('match' = route name pattern for the active state)
 *
 * Icons: see resources/views/components/admin/icon.blade.php
 */
return [

    'menu' => [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home', 'match' => 'admin.dashboard'],

        ['heading' => 'Learning'],
        ['label' => 'Courses', 'route' => 'admin.courses.index', 'icon' => 'book', 'match' => 'admin.courses.*'],
        ['label' => 'Batches', 'route' => 'admin.batches.index', 'icon' => 'layers', 'match' => 'admin.batches.*'],
        ['label' => 'Categories', 'route' => 'admin.categories.index', 'icon' => 'tag', 'match' => 'admin.categories.*'],
        ['label' => 'Content library', 'route' => 'admin.library.content.index', 'icon' => 'video', 'match' => 'admin.library.content.*'],
        ['label' => 'Resource library', 'route' => 'admin.library.resources.index', 'icon' => 'folder', 'match' => 'admin.library.resources.*'],

        ['heading' => 'People'],
        ['label' => 'Students', 'route' => 'admin.students.index', 'icon' => 'users', 'match' => 'admin.students.*'],
        ['label' => 'Instructors', 'route' => 'admin.instructors.index', 'icon' => 'user', 'match' => 'admin.instructors.*'],

        ['heading' => 'Sales'],
        ['label' => 'Orders', 'route' => 'admin.orders.index', 'icon' => 'credit-card', 'match' => 'admin.orders.*'],

        ['heading' => 'System'],
        ['label' => 'Server', 'route' => 'admin.server.index', 'icon' => 'monitor', 'match' => 'admin.server.*'],
        ['label' => 'Settings', 'route' => 'admin.settings.index', 'icon' => 'sliders', 'match' => 'admin.settings.*'],
    ],

];
