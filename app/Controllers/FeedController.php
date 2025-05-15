<?php

namespace App\Controllers;

class FeedController extends Controller
{
    /**
     * Toon de hoofdpagina van de nieuwsfeed
     */
    public function index()
    {
        // Tijdelijke dummy posts data (later uit database)
        $posts = [
            [
                'id' => 1,
                'user_id' => 1,
                'user_name' => 'Jan Jansen',
                'user_avatar' => 'avatars/2025/05/default-avatar.png',
                'content' => 'Dit is een voorbeeld van een bericht op SocialCore. Hier kunnen gebruikers hun gedachten delen!',
                'created_at' => '3 uur geleden',
                'likes' => 12,
                'comments' => 4,
                'type' => 'text'
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'user_name' => 'Petra de Vries',
                'user_avatar' => 'avatars/2025/05/default-avatar.png',
                'content' => 'Vandaag een prachtige wandeling gemaakt door het bos! De natuur is op haar mooist in deze tijd van het jaar. 🍁🌲',
                'created_at' => '5 uur geleden',
                'likes' => 24,
                'comments' => 7,
                'type' => 'text'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'user_name' => 'Thomas Bakker',
                'user_avatar' => 'avatars/2025/05/default-avatar.png',
                'content' => 'Heeft iemand tips voor goede programmeercursussen? Ik wil graag PHP leren! #codering #webontwikkeling',
                'created_at' => 'gisteren',
                'likes' => 8,
                'comments' => 15,
                'type' => 'text'
            ]
        ];
        
        // Trending hashtags
        $trending_hashtags = [
            ['tag' => 'developer', 'count' => 25980],
            ['tag' => 'seo', 'count' => 44518],
            ['tag' => 'socialmedia', 'count' => 43696],
            ['tag' => 'digitalmarketer', 'count' => 26395]
        ];
        
        // Online vrienden (dummy data)
        $online_friends = [
            ['id' => 5, 'name' => 'Deen Doughouz', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 6, 'name' => 'Wael Anjo', 'avatar' => 'avatars/2025/05/default-avatar.png']
        ];
        
        // Suggesties voor te volgen gebruikers
        $suggested_users = [
            ['id' => 7, 'name' => 'Boris Zuev', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 8, 'name' => 'Extin Ser', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 9, 'name' => 'Capital Company', 'avatar' => 'avatars/2025/05/default-avatar.png']
        ];
        
        // Stuur data naar de view
        $data = [
            'title' => 'Nieuwsfeed',
            'posts' => $posts,
            'trending_hashtags' => $trending_hashtags,
            'online_friends' => $online_friends,
            'suggested_users' => $suggested_users,
            'current_user' => [
                'id' => 1,
                'name' => 'Rudy',
                'username' => '@codingfun',
                'avatar' => 'avatars/2025/05/default-avatar.png',
                'post_count' => 18,
                'following' => 2,
                'followers' => 59
            ]
        ];
        
        $this->view('feed/index', $data);
    }
    
    /**
     * Een methode voor het later toevoegen van nieuwe posts
     */
    public function create()
    {
        // Functionaliteit voor het maken van nieuwe posts
        // Komt in een latere fase
    }
    
    /**
     * Een methode voor het ophalen van meer posts (bijv. voor oneindige scroll)
     */
    public function loadMore()
    {
        // Functionaliteit voor het laden van meer posts
        // Komt in een latere fase
    }
}