# SocialHub — Database Schema & Data Modeling Specification

## 1. Entity-Relationship Overview (Mermaid ER Diagram)

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string username UK
        string email UK
        timestamp email_verified_at
        string password
        string remember_token
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    PROFILES {
        bigint id PK
        bigint user_id FK,UK
        text bio
        string location
        string website
        string avatar_path
        string banner_path
        date birth_date
        boolean is_private
        timestamp created_at
        timestamp updated_at
    }

    POSTS {
        bigint id PK
        bigint user_id FK
        text content
        string visibility
        string reply_settings
        unsigned_int likes_count
        unsigned_int comments_count
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    POST_LIKES {
        bigint id PK
        bigint post_id FK
        bigint user_id FK
        timestamp created_at
    }

    COMMENTS {
        bigint id PK
        bigint post_id FK
        bigint user_id FK
        bigint parent_id FK,null
        text content
        unsigned_int likes_count
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    FOLLOWS {
        bigint id PK
        bigint follower_id FK
        bigint following_id FK
        string status
        timestamp created_at
        timestamp updated_at
    }

    CONVERSATIONS {
        bigint id PK
        string title
        string type
        timestamp last_message_at
        timestamp created_at
        timestamp updated_at
    }

    CONVERSATION_USER {
        bigint id PK
        bigint conversation_id FK
        bigint user_id FK
        timestamp last_read_at
        boolean is_muted
        timestamp created_at
        timestamp updated_at
    }

    MESSAGES {
        bigint id PK
        bigint conversation_id FK
        bigint user_id FK
        text body
        string type
        boolean is_read
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    NOTIFICATIONS {
        char id PK
        string type
        string notifiable_type
        bigint notifiable_id
        text data
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }

    MEDIA {
        bigint id PK
        string mediable_type
        bigint mediable_id
        string file_path
        string disk
        string file_name
        string mime_type
        unsigned_bigint file_size
        json meta
        timestamp created_at
        timestamp updated_at
    }

    USERS ||--|| PROFILES : "has one"
    USERS ||--o{ POSTS : "authors"
    USERS ||--o{ POST_LIKES : "submits"
    USERS ||--o{ COMMENTS : "writes"
    USERS ||--o{ FOLLOWS : "follower"
    USERS ||--o{ FOLLOWS : "following"
    USERS ||--o{ CONVERSATION_USER : "joins"
    CONVERSATIONS ||--o{ CONVERSATION_USER : "contains"
    CONVERSATIONS ||--o{ MESSAGES : "houses"
    USERS ||--o{ MESSAGES : "sends"
    POSTS ||--o{ POST_LIKES : "receives"
    POSTS ||--o{ COMMENTS : "holds"
    COMMENTS ||--o{ COMMENTS : "replies to"
    POSTS ||--o{ MEDIA : "polymorphic attaches"
    USERS ||--o{ NOTIFICATIONS : "receives"
```

---

## 2. Table Specifications & Indexing Strategy

### 2.1 `users`
Core identity table storing credentials and account status.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Unique user identifier |
| `name` | `VARCHAR(255)` | Not Null | Display name (e.g. "Sophia Vance") |
| `username` | `VARCHAR(30)` | Unique, Not Null, Index | Unique handle (e.g. "sophiav") |
| `email` | `VARCHAR(255)` | Unique, Not Null, Index | Primary account email |
| `email_verified_at` | `TIMESTAMP` | Nullable | Email confirmation timestamp |
| `password` | `VARCHAR(255)` | Not Null | Hashed password string |
| `remember_token` | `VARCHAR(100)` | Nullable | Persistent login token |
| `created_at` | `TIMESTAMP` | Nullable | Account creation date |
| `updated_at` | `TIMESTAMP` | Nullable | Last modification date |
| `deleted_at` | `TIMESTAMP` | Nullable, Index | Soft delete timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `UNIQUE KEY users_username_unique (username)`
- `UNIQUE KEY users_email_unique (email)`
- `INDEX users_deleted_at_index (deleted_at)`

---

### 2.2 `profiles`
Extended user metadata, bio, avatar, and privacy configurations.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Profile record ID |
| `user_id` | `BIGINT UNSIGNED` | Unique, Foreign Key (`users.id`), On Delete Cascade | Owning user |
| `bio` | `VARCHAR(320)` | Nullable | User self-description |
| `location` | `VARCHAR(100)` | Nullable | City / Country display |
| `website` | `VARCHAR(255)` | Nullable | Personal / company website URL |
| `avatar_path` | `VARCHAR(255)` | Nullable | Path to avatar image |
| `banner_path` | `VARCHAR(255)` | Nullable | Path to header banner image |
| `birth_date` | `DATE` | Nullable | User date of birth |
| `is_private` | `BOOLEAN` | Default `false` | Whether posts require follow approval |
| `created_at` | `TIMESTAMP` | Nullable | Record creation |
| `updated_at` | `TIMESTAMP` | Nullable | Record modification |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `UNIQUE KEY profiles_user_id_unique (user_id)`
- `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`

---

### 2.3 `posts`
User status updates, articles, and media shares.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Unique post ID |
| `user_id` | `BIGINT UNSIGNED` | Foreign Key (`users.id`), On Delete Cascade | Post author |
| `content` | `TEXT` | Not Null | Markdown / text body (max 2,000 chars) |
| `visibility` | `ENUM('public', 'followers', 'private')` | Default `'public'`, Index | Audience filter |
| `reply_settings` | `ENUM('everyone', 'followers', 'mentioned')`| Default `'everyone'` | Reply permissions |
| `likes_count` | `INT UNSIGNED` | Default `0` | Denormalized like counter |
| `comments_count` | `INT UNSIGNED` | Default `0` | Denormalized comment counter |
| `created_at` | `TIMESTAMP` | Nullable, Index | Post publish date |
| `updated_at` | `TIMESTAMP` | Nullable | Post edit timestamp |
| `deleted_at` | `TIMESTAMP` | Nullable, Index | Soft delete timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `INDEX posts_user_id_created_at_idx (user_id, created_at DESC)` (Optimizes user profile post feed)
- `INDEX posts_visibility_created_at_idx (visibility, created_at DESC)` (Optimizes explore & public feed queries)
- `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`

---

### 2.4 `post_likes`
Pivot recording user likes on posts.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Like record ID |
| `post_id` | `BIGINT UNSIGNED` | Foreign Key (`posts.id`), On Delete Cascade | Target post |
| `user_id` | `BIGINT UNSIGNED` | Foreign Key (`users.id`), On Delete Cascade | Liking user |
| `created_at` | `TIMESTAMP` | Nullable | Like timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `UNIQUE KEY post_likes_post_user_unique (post_id, user_id)` (Prevents duplicate likes)
- `INDEX post_likes_user_id_idx (user_id)` (Quickly fetch all posts liked by a user)
- `FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE`
- `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`

---

### 2.5 `comments`
Threaded discussion comments and replies on posts.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Comment ID |
| `post_id` | `BIGINT UNSIGNED` | Foreign Key (`posts.id`), On Delete Cascade | Target post |
| `user_id` | `BIGINT UNSIGNED` | Foreign Key (`users.id`), On Delete Cascade | Comment author |
| `parent_id` | `BIGINT UNSIGNED` | Nullable, Foreign Key (`comments.id`), On Delete Cascade | Parent comment for replies |
| `content` | `TEXT` | Not Null | Comment text (max 500 chars) |
| `likes_count` | `INT UNSIGNED` | Default `0` | Denormalized like counter |
| `created_at` | `TIMESTAMP` | Nullable | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Nullable | Modification timestamp |
| `deleted_at` | `TIMESTAMP` | Nullable | Soft delete timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `INDEX comments_post_id_created_at_idx (post_id, created_at ASC)` (Fast chronological thread retrieval)
- `INDEX comments_parent_id_idx (parent_id)` (Retrieves nested child replies)
- `FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE`
- `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`
- `FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE`

---

### 2.6 `follows`
Social graph relationships between users.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Relationship ID |
| `follower_id` | `BIGINT UNSIGNED` | Foreign Key (`users.id`), On Delete Cascade | Initiating follower |
| `following_id` | `BIGINT UNSIGNED` | Foreign Key (`users.id`), On Delete Cascade | Target followed user |
| `status` | `ENUM('accepted', 'pending')` | Default `'accepted'`, Index | Follow approval state |
| `created_at` | `TIMESTAMP` | Nullable | Relationship creation |
| `updated_at` | `TIMESTAMP` | Nullable | State change date |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `UNIQUE KEY follows_follower_following_unique (follower_id, following_id)` (No duplicate follows)
- `INDEX follows_following_follower_idx (following_id, follower_id)` (Bidirectional relationship lookup)
- `FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE`
- `FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE`

---

### 2.7 `conversations`
Direct and group chat rooms.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Conversation ID |
| `title` | `VARCHAR(100)` | Nullable | Optional name for group conversations |
| `type` | `ENUM('direct', 'group')` | Default `'direct'` | Chat mode |
| `last_message_at` | `TIMESTAMP` | Nullable, Index | Timestamp of newest message for inbox ordering |
| `created_at` | `TIMESTAMP` | Nullable | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Nullable | Modification timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `INDEX conversations_last_message_at_idx (last_message_at DESC)`

---

### 2.8 `conversation_user` (Pivot)
Membership mapping and participant settings in conversations.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Pivot row ID |
| `conversation_id` | `BIGINT UNSIGNED` | Foreign Key (`conversations.id`), On Delete Cascade | Target conversation |
| `user_id` | `BIGINT UNSIGNED` | Foreign Key (`users.id`), On Delete Cascade | Participant user |
| `last_read_at` | `TIMESTAMP` | Nullable | Timestamp to track unread message counts |
| `is_muted` | `BOOLEAN` | Default `false` | Notification mute toggle |
| `created_at` | `TIMESTAMP` | Nullable | Join timestamp |
| `updated_at` | `TIMESTAMP` | Nullable | Update timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `UNIQUE KEY conv_user_unique (conversation_id, user_id)`
- `INDEX conv_user_user_idx (user_id, last_read_at)` (High performance inbox query)
- `FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE`
- `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`

---

### 2.9 `messages`
Individual chat messages sent within conversations.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Message ID |
| `conversation_id` | `BIGINT UNSIGNED` | Foreign Key (`conversations.id`), On Delete Cascade | Parent conversation |
| `user_id` | `BIGINT UNSIGNED` | Foreign Key (`users.id`), On Delete Cascade | Message sender |
| `body` | `TEXT` | Not Null | Message body content |
| `type` | `ENUM('text', 'media', 'system')` | Default `'text'` | Message type |
| `is_read` | `BOOLEAN` | Default `false` | Quick read flag |
| `created_at` | `TIMESTAMP` | Nullable, Index | Dispatch timestamp |
| `updated_at` | `TIMESTAMP` | Nullable | Edit timestamp |
| `deleted_at` | `TIMESTAMP` | Nullable | Soft delete timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `INDEX messages_conv_created_idx (conversation_id, created_at ASC)` (Fast message history pagination)
- `FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE`
- `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`

---

### 2.10 `notifications`
Standard Laravel database notification store with custom indexed access.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `CHAR(36)` | Primary Key (UUID) | Unique notification UUID |
| `type` | `VARCHAR(255)` | Not Null | Fully qualified notification class name |
| `notifiable_type` | `VARCHAR(255)` | Not Null | Target entity class (`App\Models\User`) |
| `notifiable_id` | `BIGINT UNSIGNED` | Not Null | Target user ID |
| `data` | `TEXT` | Not Null | Serialized JSON notification payload |
| `read_at` | `TIMESTAMP` | Nullable, Index | Read confirmation timestamp |
| `created_at` | `TIMESTAMP` | Nullable | Dispatched timestamp |
| `updated_at` | `TIMESTAMP` | Nullable | Updated timestamp |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `INDEX notifiable_index (notifiable_type, notifiable_id, read_at)`

---

### 2.11 `media`
Polymorphic asset registry managing all image uploads.

| Column | Type | Attributes / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Media ID |
| `mediable_type` | `VARCHAR(255)` | Not Null | Morph model (`App\Models\Post`, `Message`) |
| `mediable_id` | `BIGINT UNSIGNED` | Not Null | Morph model ID |
| `file_path` | `VARCHAR(255)` | Not Null | Storage relative file path |
| `disk` | `VARCHAR(50)` | Default `'public'` | Storage disk driver |
| `file_name` | `VARCHAR(255)` | Not Null | Original filename |
| `mime_type` | `VARCHAR(100)` | Not Null | Validated MIME type |
| `file_size` | `BIGINT UNSIGNED` | Not Null | Size in bytes |
| `meta` | `JSON` | Nullable | Width, height, aspect ratio, blurhash |
| `created_at` | `TIMESTAMP` | Nullable | Upload date |
| `updated_at` | `TIMESTAMP` | Nullable | Update date |

**Indexes & Constraints**:
- `PRIMARY KEY (id)`
- `INDEX media_mediable_idx (mediable_type, mediable_id)`

---

## 3. Eloquent Model Relationships & Scopes

```php
// User.php
public function profile(): HasOne { return $this->hasOne(Profile::class); }
public function posts(): HasMany { return $this->hasMany(Post::class); }
public function comments(): HasMany { return $this->hasMany(Comment::class); }
public function postLikes(): HasMany { return $this->hasMany(PostLike::class); }
public function followers(): BelongsToMany { return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withPivot('status')->withTimestamps(); }
public function following(): BelongsToMany { return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withPivot('status')->withTimestamps(); }
public function conversations(): BelongsToMany { return $this->belongsToMany(Conversation::class)->withPivot('last_read_at', 'is_muted')->withTimestamps(); }
public function messages(): HasMany { return $this->hasMany(Message::class); }

// Post.php
public function user(): BelongsTo { return $this->belongsTo(User::class); }
public function comments(): HasMany { return $this->hasMany(Comment::class); }
public function likes(): HasMany { return $this->hasMany(PostLike::class); }
public function media(): MorphMany { return $this->morphMany(Media::class, 'mediable'); }

// Follow.php
public function follower(): BelongsTo { return $this->belongsTo(User::class, 'follower_id'); }
public function following(): BelongsTo { return $this->belongsTo(User::class, 'following_id'); }

// Conversation.php
public function users(): BelongsToMany { return $this->belongsToMany(User::class)->withPivot('last_read_at', 'is_muted')->withTimestamps(); }
public function messages(): HasMany { return $this->hasMany(Message::class); }
public function latestMessage(): HasOne { return $this->hasOne(Message::class)->latestOfMany(); }
```

