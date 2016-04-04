<?php

print $twig->render(
    'home.twig',
    [
        'body_class' => 'home',
        'locales'    => $locales,
    ]
);
