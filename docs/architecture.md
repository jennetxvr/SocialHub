# SocialHub — System Architecture Specification

## 1. Executive Overview & Product Vision

**SocialHub** is a modern, high-performance social networking and real-time messaging platform. The platform is engineered to deliver a fluid, responsive user experience with an elegant aesthetic centered around a sophisticated violet/indigo design palette.

### Core Value Pillars
- **Social Discovery & Interaction**: Dynamic personalized feed, media-rich posts, nested discussions, engagement metrics, and discovery mechanisms.
- **Real-Time Connectivity**: Instant 1-on-1 and group direct messaging, live typing indicators, presence tracking, and instant push notifications powered by WebSockets.
- **Enterprise-Grade Engineering**: Strict Laravel conventions, typed domain actions, comprehensive authorization policies, robust media pipeline, and automated test coverage via Pest.

---

## 2. Technology Stack & Package Architecture

| Component | Technology | Version / Specification | Role |
| :--- | :--- | :--- | :--- |
| **Backend Framework** | Laravel | 13.x | Core application framework, routing, ORM, queue, broadcasting |
| **Runtime Language** | PHP | 8.5.x | Server runtime with typed properties, match expressions, JIT |
| **Relational Database** | MySQL | 8.0+ / 8.4 LTS | Primary relational storage engine with ACID compliance |
| **Frontend Rendering** | Blade + Alpine.js | Modern Blade Components | Server-rendered UI with reactive micro-interactions |
| **CSS Framework** | Tailwind CSS | v4.x (`@tailwindcss/vite`) | Design system styling with custom violet/indigo theme tokens |
| **Asset Bundler** | Vite | 8.x | High-speed frontend compiler and HMR |
| **Real-Time WebSockets** | Laravel Reverb | Official First-Party | Scalable, high-throughput WebSocket server |
| **Client WebSockets** | Laravel Echo + Pusher JS | Latest | Client-side event subscription and state binding |
| **Image Processing** | Intervention Image | v3.x | Image manipulation, thumbnailing, webp conversion |
| **Testing Harness** | Pest PHP | v5.x | Behavioral and unit test suite |
| **Version Control** | Git | 2.54+ | Source code versioning and branch management |

---

## 3. Application Structure & Folder Organization

The application adopts an **Action-Domain-Driven architecture** within Laravel's native structure. Business logic is decoupled from controllers and encapsulated in single-responsibility Action classes.

```
social-hub/
├── app/
│   ├── Actions/                  # Single-responsibility business transactions
│   │   ├── Auth/                 # RegisterUserAction, AuthenticateUserAction
│   │   ├── Post/                 # CreatePostAction, DeletePostAction, ToggleLikeAction
│   │   ├── Comment/              # CreateCommentAction, DeleteCommentAction
│   │   ├── Message/              # SendMessageAction, MarkConversationReadAction
│   │   ├── User/                 # FollowUserAction, UnfollowUserAction, UpdateProfileAction
│   │   └── Media/                # ProcessUploadAction, GenerateThumbnailAction
│   ├── Events/                   # Domain events for real-time and background jobs
│   │   ├── MessageSent.php
│   │   ├── UserTyping.php
│   │   ├── PostLiked.php
│   │   ├── CommentCreated.php
│   │   └── UserFollowed.php
│   ├── Http/
│   │   ├── Controllers/          # Thin controllers delegating to Actions
│   │   │   ├── Auth/             # LoginController, RegisterController, PasswordResetController
│   │   │   ├── FeedController.php
│   │   │   ├── ExploreController.php
│   │   │   ├── PostController.php
│   │   │   ├── CommentController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── FollowController.php
│   │   │   ├── ConversationController.php
│   │   │   ├── MessageController.php
│   │   │   ├── NotificationController.php
│   │   │   └── SettingsController.php
│   │   ├── Middleware/           # Custom middleware (EnsureProfileComplete, OnlineStatus)
│   │   └── Requests/             # Form Requests with strict validation rules
│   │       ├── Auth/
│   │       ├── Post/
│   │       ├── Comment/
│   │       ├── Message/
│   │       └── Settings/
│   ├── Jobs/                     # Queued background tasks
│   │   ├── OptimizeMediaJob.php
│   │   ├── SendPushNotificationJob.php
│   │   └── PruneStaleUploadsJob.php
│   ├── Models/                   # Eloquent Models with explicit typing & relationships
│   │   ├── User.php
│   │   ├── Profile.php
│   │   ├── Post.php
│   │   ├── PostLike.php
│   │   ├── Comment.php
│   │   ├── Follow.php
│   │   ├── Conversation.php
│   │   ├── Message.php
│   │   ├── Notification.php
│   │   └── Media.php
│   ├── Notifications/            # Multi-channel notification classes
│   │   ├── PostLikedNotification.php
│   │   ├── NewCommentNotification.php
│   │   ├── NewFollowerNotification.php
│   │   └── MentionNotification.php
│   ├── Policies/                 # Strict authorization rules
│   │   ├── PostPolicy.php
│   │   ├── CommentPolicy.php
│   │   ├── ConversationPolicy.php
│   │   └── UserPolicy.php
│   ├── Providers/                # Service providers
│   │   └── BroadcastServiceProvider.php
│   └── View/Components/          # Blade class-based components
├── config/                       # Application configuration
├── database/
│   ├── factories/                # Model factories for Pest testing
│   ├── migrations/               # Structured database schema migrations
│   └── seeders/                  # Demo and test database seeders
├── docs/                         # System documentation and diagrams
│   ├── architecture.md
│   ├── database-schema.md
│   └── development-roadmap.md
├── resources/
│   ├── css/
│   │   └── app.css               # Tailwind CSS v4 design tokens and theme rules
│   ├── js/
│   │   ├── app.js                # Bootstrap scripts, Echo initialization
│   │   ├── echo.js               # Reverb connection config
│   │   └── components/           # Micro-interactive scripts (chat, notifications)
│   └── views/
│       ├── layouts/              # app.blade.php, guest.blade.php, chat.blade.php
│       ├── components/           # Atomic UI elements (button, input, modal, avatar)
│       ├── auth/                 # login, register, forgot-password
│       ├── feed/                 # index, partials/post-card, partials/create-post
│       ├── explore/              # index, search-results
│       ├── profile/              # show, edit, followers, following
│       ├── messages/             # index, conversation-window, message-bubble
│       ├── notifications/        # index, notification-item
│       └── settings/             # account, security, privacy
├── routes/
│   ├── web.php                   # Web and UI routes with session middleware
│   ├── channels.php              # WebSocket authorization channels
│   └── console.php               # Scheduled jobs and artisan maintenance commands
└── tests/
    ├── Feature/                  # Feature and integration Pest tests
    └── Unit/                     # Isolated unit tests for actions & domain services
```

---

## 4. Authentication & Security Strategy

### 4.1 Authentication Mechanism
- **Stateful Session Authentication**: Uses Laravel's built-in session guard (`web`) with encrypted, HTTP-only, SameSite=Lax session cookies.
- **Credential Storage**: Passwords hashed using Bcrypt with a work factor of 12 (or Argon2id where supported).
- **Brute Force Protection**: Dynamic rate limiting on authentication routes:
  - Login: max 5 attempts per minute per IP + email hash combination (`throttle:login`).
  - Registration: max 3 registrations per hour per IP.
  - Password Reset: max 3 attempts per hour.
- **Session Security**: Session fixation mitigation via `session()->regenerate()` on every login and privilege escalation; session invalidation across all devices on password modification.

### 4.2 Authorization Strategy
Every user action is governed by dedicated Laravel Policies using method-level permission checks:

| Policy | Method | Rule / Condition |
| :--- | :--- | :--- |
| **PostPolicy** | `view` | Public post OR author is followed by viewer (if private account) |
| | `update` / `delete` | Author ID === Authenticated User ID |
| **CommentPolicy** | `delete` | Comment author OR Post author (can moderate their own post's thread) |
| **ConversationPolicy**| `view` / `send` | User must exist in `conversation_user` pivot table |
| | `delete` | Conversation participant |
| **UserPolicy** | `follow` | Target user !== Authenticated user AND not blocked |
| | `updateProfile` | Target user ID === Authenticated User ID |

---

## 5. Real-Time Messaging Architecture

The messaging system is designed for high-concurrency, low-latency communication using **Laravel Reverb** as the WebSocket broker and **Laravel Echo** on the client.

```
┌─────────────────┐       HTTP POST        ┌─────────────────────┐
│  Client Browser │ ─────────────────────> │  MessageController  │
│  (Alpine/Blade) │                        │ (SendMessageAction) │
└─────────────────┘                        └──────────┬──────────┘
         ▲                                            │
         │                                            ▼
         │ WebSocket Push                  ┌─────────────────────┐
         │ (Echo Subscription)             │ Save to MySQL (DB)  │
         │                                 └──────────┬──────────┘
┌────────┴────────┐       Broadcast Event             │
│ Laravel Reverb  │ <─────────────────────────────────┘
│ WebSocket Server│   (MessageSent Event via Redis/Queue)
└─────────────────┘
```

### Channel Definitions (`routes/channels.php`)
1. **Direct Conversation Channel**: `private-conversation.{conversationId}`
   - Authorizes only participants verified in `conversation_user`.
   - Broadcasts `MessageSent`, `MessageRead`, `MessageDeleted`.
2. **Presence Channel for Typing & Online State**: `presence-conversation.{conversationId}`
   - Broadcasts member joined, member left, and client-side whisper events for typing (`whisper('typing', { user })`).
3. **User Notification Channel**: `private-user.{userId}`
   - Broadcasts personal real-time notification toasts and badge increments.

---

## 6. File Upload & Media Pipeline

### 6.1 Storage Strategy
- **Disks**: Development uses `local`/`public` storage disk (`storage/app/public/`) with symlink to `public/storage`. Production allows zero-code transition to Amazon S3 or Cloudflare R2 via Laravel's Filesystem abstraction.
- **Polymorphic Media Schema**: A dedicated `media` table attaches media records to `Post`, `User` (avatar/banner), or `Message`.

### 6.2 Processing Pipeline
1. **Upload Validation**:
   - Allowed MIME types: `image/jpeg`, `image/png`, `image/webp`, `image/gif`.
   - Max file size: 8 MB for post photos, 2 MB for avatars/banners.
   - Validation performed via server-side file inspection (`finfo`), bypassing client-declared MIME headers.
2. **Asynchronous Image Transformation**:
   - Uses `Intervention Image v3` inside a queued background job (`OptimizeMediaJob`).
   - Generates 3 versions:
     - `original`: Stripped of EXIF metadata for privacy, compressed with 85% WebP quality.
     - `medium` (800px width constraint): Standard feed display.
     - `thumb` (200x200px crop): Grid views and preview cards.
3. **Safety & Storage Cleansing**:
   - File names are sanitized with cryptographic UUIDs (`Str::uuid() . '.webp'`).
   - Orphaned uploads cleaned periodically via `PruneStaleUploadsJob`.

---

## 7. Notification Architecture

SocialHub implements an aggregated multi-channel notification engine.

### 7.1 Notification Types
- `PostLikedNotification`: Triggered when an active user likes another user's post.
- `NewCommentNotification`: Triggered when a post receives a comment or a comment reply.
- `NewFollowerNotification`: Triggered when a user gains a follower.
- `MentionNotification`: Triggered when a user is tagged with `@username`.

### 7.2 Channels Dispatched
- **Database (`database`)**: Stores JSON payload in `notifications` table for in-app notification center.
- **Broadcast (`broadcast`)**: Dispatched across `private-user.{id}` via Reverb to trigger instant popups and counter badge increments.
- **Mail (`mail`)**: Optional queued transactional email digest for offline users.

### 7.3 Debouncing & Idempotency
- Multiple likes or duplicate actions within a short time window are debounced to prevent notification spam.
- Self-notifications (user liking their own post or commenting on their own thread) are suppressed at the Action level.

