<?php
return [
    '5000' => [
        'file' => 'data/IMDB-5000/movie_metadata.csv',
        'delimiter' => ',',
        'multivalued' => [
            'genres' => '|',
            'plot_keywords' => '|'
        ]

    ],
    '1000' => [
        'file' => 'data/IMDB-1000/IMDB-Movie-Data.csv',
        'delimiter' => ','
    ],
    'AWS' => [
        'name_basics' => [
            'file' => 'data/IMDB-AWS/name.basics.tsv/data.tsv',
            'delimiter' => "\t"
        ],

        'title_basics' => [
            'file' => 'data/IMDB-AWS/title.basics.tsv/data.tsv',
            'delimiter' => "\t"
        ]
        ,

        'title_crew' => [
            'file' => 'data/IMDB-AWS/title.crew.tsv/data.tsv',
            'delimiter' => "\t"
        ]
        ,

        'title_episode' => [
            'file' => 'data/IMDB-AWS/title.episode.tsv/data.tsv',
            'delimiter' => "\t"
        ]
        ,

        'title_principals' => [
            'file' => 'data/IMDB-AWS/title.principals.tsv/data.tsv',
            'delimiter' => "\t"
        ]
        ,

        'title_ratings' => [
            'file' => 'data/IMDB-AWS/title.ratings.tsv/data.tsv',
            'delimiter' => "\t"
        ]

    ]
];