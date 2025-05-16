<?php

namespace App\Controllers;

class FeedController extends Controller
{
    /**
     * Toon de hoofdpagina van de nieuwsfeed
     */
    public function index()
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
            return;
        }
        
        // Dummy data voor huidige gebruiker
        $current_user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['username'] ?? 'Gebruiker',
            'username' => $_SESSION['username'] ?? 'gebruiker',
            'avatar' => isset($_SESSION['avatar']) ? $_SESSION['avatar'] : 'avatars/2025/05/default-avatar.png',
            'post_count' => 18,
            'following' => 42,
            'followers' => 29
        ];
        
        // Dummy posts data (later uit database)
        $posts = [
            [
                'id' => 1,
                'user_id' => 1,
                'user_name' => 'Jan Jansen',
                'user_avatar' => 'avatars/2025/05/default-avatar.png',
                'content' => 'Welkom bij de nieuwe SocialCore feed in Hyves-stijl! Dit doet me echt denken aan de goeie oude tijd. Wie herinnert zich nog de krabbels en gadgets?',
                'created_at' => '2 uur geleden',
                'likes' => 12,
                'comments' => 4,
                'type' => 'text'
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'user_name' => 'Petra de Vries',
                'user_avatar' => 'avatars/2025/05/default-avatar.png',
                'content' => "Net helemaal nostalgisch geworden door de nieuwe Hyves-stijl interface! 😊\n\nWie herinnert zich nog: 'Wie heeft de langste naam?' en 'Wat is je favoriete kleur?'",
                'created_at' => '4 uur geleden',
                'likes' => 24,
                'comments' => 7,
                'type' => 'text'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'user_name' => 'Thomas Bakker',
                'user_avatar' => 'avatars/2025/05/default-avatar.png',
                'content' => 'SocialCore combineert het beste van Hyves met moderne technologie. Open source, privacy-vriendelijk en gemaakt in Nederland! #webdevelopment #opensource',
                'created_at' => 'gisteren',
                'likes' => 18,
                'comments' => 5,
                'type' => 'text'
            ]
        ];
        
        // Trending hashtags (dummy data)
        $trending_hashtags = [
            ['tag' => 'socialcore', 'count' => 1245],
            ['tag' => 'hyvesstijl', 'count' => 876],
            ['tag' => 'nostalgie', 'count' => 654],
            ['tag' => 'nederland', 'count' => 432],
            ['tag' => 'oldskool', 'count' => 321]
        ];
        
        // Online vrienden (dummy data)
        $online_friends = [
            ['id' => 5, 'name' => 'Emma Visser', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 6, 'name' => 'Lucas de Jong', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 7, 'name' => 'Sophie Mulder', 'avatar' => 'avatars/2025/05/default-avatar.png']
        ];
        
        // Suggesties voor te volgen gebruikers
        $suggested_users = [
            ['id' => 8, 'name' => 'Tim Vos', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 9, 'name' => 'Nina Smit', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 10, 'name' => 'Robin Kok', 'avatar' => 'avatars/2025/05/default-avatar.png'],
            ['id' => 11, 'name' => 'Laura Hendriks', 'avatar' => 'avatars/2025/05/default-avatar.png']
        ];
        
        // Data voor de view
        $data = [
            'title' => 'Nieuwsfeed',
            'current_user' => $current_user,
            'posts' => $posts,
            'trending_hashtags' => $trending_hashtags,
            'online_friends' => $online_friends,
            'suggested_users' => $suggested_users,
            'active_menu' => 'feed'
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